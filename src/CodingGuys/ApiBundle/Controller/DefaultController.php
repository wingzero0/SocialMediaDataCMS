<?php

namespace CodingGuys\ApiBundle\Controller;

use AppBundle\Controller\AppBaseController;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Api Login controller.
 *
 * @Route("/mobile")
 */
class DefaultController extends AppBaseController
{
    /**
     * @ApiDoc(
     *  description="home page feed",
     *  parameters={
     *      {"name"="tags[]", "dataType"="string", "required"=false, "description"="filter by tag"},
     *      {"name"="days", "dataType"="int", "required"=false, "description"="filter post within x days"},
     *      {"name"="limit", "dataType"="int", "required"=false, "description"="return x posts"},
     *      {"name"="skip", "dataType"="int", "required"=false, "description"="skip first x posts"},
     *  }
     * )
     * @Route("/home/", name="@apiHome")
     * @Method("GET")
     */
    public function indexAction(Request $request){
        $qb = $this->getPostRepo()->getPublicQueryBuilderSortWithRank();
        $qb = $this->compileFilter($request, $qb);
        $limit = intval($request->get("limit"));
        if ($limit <= 0){
            $limit = 25;
        }
        $posts = $qb
            ->limit($limit)
            ->getQuery()->execute();
        $ret = array();
        foreach($posts as $post){
            $ret[] = $post;
        }
        $serialize = $this->getJMSSerializer()->serialize(
            array('data' => $ret),
            'json',
            SerializationContext::create()->setGroups(array('display'))
        );
        return new Response($serialize);
    }

    /**
     * @param Request $request
     * @param Builder $qb
     * @return Builder
     */
    private function compileFilter(Request $request, Builder $qb){
        $qb->field('publishStatus')->equals('published');
        $tags = $request->get('tags');
        if (!empty($tags)){
            foreach($tags as $tag){
                $tagTrim = trim($tag);
                if (!empty($tagTrim)){
                    $qb->addAnd(
                        $qb->expr()->field('tags')->equals($tagTrim)
                    );
                }
            }
        }else{
            $qb->field('showAtHomepage')->equals(true);
        }

        $interval = intval($request->get("interval"));
        $this->getLogger()->info("interval");
        if (!empty($interval)){
            $this->getLogger()->info($interval);
            $nowDate = new \DateTime();
            $createDate = $nowDate->sub(new \DateInterval("P". $interval ."D"));
            $qb->field("createAt")->gte($createDate);
        }
        return $qb;
    }
}

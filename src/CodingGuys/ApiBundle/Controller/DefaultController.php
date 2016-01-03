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
     *      {"name"="days", "dataType"="int", "required"=false, "description"="filter post within x days"},
     *      {"name"="limit", "dataType"="int", "required"=false, "description"="return x posts"},
     *      {"name"="skip", "dataType"="int", "required"=false, "description"="skip first x posts"},
     *  }
     * )
     * @Route("/", name="@apiHome")
     * @Method("GET")
     */
    public function indexAction(Request $request){
        $qb = $this->createQueryBuilder($request);
        $qb->field('showAtHomepage')->equals(true);
        $posts = $qb->getQuery()->execute();
        $ret = array();
        foreach($posts as $post){
            $ret[] = $post;
        }
        $serialize = $this->serialize($ret, "display");
        return new Response($serialize);
    }

    /**
     * @ApiDoc(
     *  description="search feed with tags",
     *  parameters={
     *      {"name"="tags[]", "dataType"="string", "required"=false, "description"="filter by tag"},
     *      {"name"="days", "dataType"="int", "required"=false, "description"="filter post within x days"},
     *      {"name"="limit", "dataType"="int", "required"=false, "description"="return x posts"},
     *      {"name"="skip", "dataType"="int", "required"=false, "description"="skip first x posts"},
     *  }
     * )
     * @Route("/tags", name="api_search_post_by_tag")
     * @Method("GET")
     */
    public function searchTagsAction(Request $request){
        $qb = $this->createQueryBuilder($request);
        $posts = $qb->getQuery()->execute();
        $ret = array();
        foreach($posts as $post){
            $ret[] = $post;
        }
        $serialize = $this->serialize($ret, "display");
        return new Response($serialize);
    }

    /**
     * @param array $data
     * @param string|null $groupName
     * @return string
     */
    private function serialize($data, $groupName = null){
        if ($groupName){
            $serialize = $this->getJMSSerializer()->serialize(
                array('data' => $data),
                'json',
                SerializationContext::create()->setGroups(array($groupName))
            );
        }else{
            $serialize = $this->getJMSSerializer()->serialize(
                array('data' => $data),
                'json'
            );
        }

        return $serialize;
    }

    /**
     * @param Request $request
     * @return Builder
     */
    private function createQueryBuilder(Request $request){
        $qb = $this->getPostRepo()->getPublicQueryBuilderSortWithRank();
        $qb = $this->compileFilter($request, $qb);
        $limit = intval($request->get("limit"));
        if ($limit <= 0){
            $limit = 25;
        }
        $skip = intval($request->get("skip"));
        if ($skip <= 0){
            $skip = 0;
        }
        $qb->limit($limit)->skip($skip);
        return $qb;
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
        }

        $interval = intval($request->get("days"));
        $this->getLogger()->info("days");
        if (!empty($interval)){
            $this->getLogger()->info($interval);
            $nowDate = new \DateTime();
            $createDate = $nowDate->sub(new \DateInterval("P". $interval ."D"));
            $qb->field("createAt")->gte($createDate);
        }
        return $qb;
    }
}

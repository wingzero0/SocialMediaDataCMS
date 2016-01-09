<?php

namespace CodingGuys\ApiBundle\Controller;

use AppBundle\Controller\AppBaseController;
use AppBundle\Document\ManagedTag;
use AppBundle\Document\Post;
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
     * @Route("/posts/hot", name="api_homepage_post")
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
     *  description="query all post, can be filter by tags",
     *  parameters={
     *      {"name"="tags[]", "dataType"="string", "required"=false, "description"="filter by tag"},
     *      {"name"="days", "dataType"="int", "required"=false, "description"="filter post within x days"},
     *      {"name"="limit", "dataType"="int", "required"=false, "description"="return x posts"},
     *      {"name"="skip", "dataType"="int", "required"=false, "description"="skip first x posts"},
     *  }
     * )
     * @Route("/posts", name="api_all_post")
     * @Method("GET")
     */
    public function getPostsAction(Request $request){
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
     * @ApiDoc(
     *  description="query a post by id",
     *  parameters={
     *      {"name"="tags[]", "dataType"="string", "required"=false, "description"="filter by tag"},
     *  }
     * )
     * @Route("/posts/{id}", name="api_specific_post")
     * @Method("GET")
     */
    public function getPostAction(Request $request,$id){
        $post = $this->getPostRepo()->find($id);
        if (!($post instanceof Post)){
            throw $this->createNotFoundException("Unable to find Post document.");
        }
        $serialize = $this->serialize($post, "display");
        return new Response($serialize);
    }

    /**
     * @ApiDoc(
     *  description="query all managed tag, with total number of tag's post",
     *  parameters={
     *      {"name"="limit", "dataType"="int", "required"=false, "description"="return x tags"},
     *      {"name"="skip", "dataType"="int", "required"=false, "description"="skip first x tags"},
     *  }
     * )
     * @Route("/tags", name="api_managedTag")
     * @Method("GET")
     */
    public function getManagedTagsAction(Request $request){
        $limit = intval($request->get("limit"));
        if ($limit <= 0){
            $limit = 25;
        }
        $skip = intval($request->get("skip"));
        if ($skip <= 0){
            $skip = 0;
        }
        return new Response($this->createMangedTagsQueryBuilder($limit, $skip));
    }

    private function createMangedTagsQueryBuilder($limit, $skip){
        $qb = $this->getManagedTagRepo()->getFindAllQueryBuilder();
        $qb->limit($limit)->skip($skip);
        $managedTags = $qb->getQuery()->execute();

        $data = array();

        foreach($managedTags as $managedTag){
            if ($managedTag instanceof ManagedTag){
                $count = $this->getPostRepo()->queryCountOfTagedPost(array($managedTag->getKey()));
                $data[] = array("tag" => $managedTag, "count" => $count);
            }
        }
        return $this->serialize($data, "display");
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

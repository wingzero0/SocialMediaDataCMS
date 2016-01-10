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
 * @Route("/tags")
 */
class TagController extends AppBaseController{

    /**
     * @ApiDoc(
     *  description="query all managed tag, with total number of tag's post",
     *  parameters={
     *      {"name"="limit", "dataType"="int", "required"=false, "description"="return x tags, default is 25"},
     *      {"name"="skip", "dataType"="int", "required"=false, "description"="skip first x tags, default is 0"},
     *  }
     * )
     * @Route("/", name="api_managedTag")
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
}

<?php

namespace CodingGuys\ApiBundle\Controller;

use AppBundle\Controller\AppBaseController;
use AppBundle\Document\ManagedTag;
use AppBundle\Proto\TagWithCountDataProto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Api ManagedTag controller.
 *
 * @Route("/tags")
 */
class ManagedTagController extends AppBaseController
{

    public function __construct(Container $container = null)
    {
        $this->setContainer($container);
    }

    /**
     * @ApiDoc(
     *  description="query all managed tag, with total number of tag's post",
     *  parameters={
     *      {"name"="limit", "dataType"="int", "required"=false, "description"="return x tags, default is 25"},
     *      {"name"="skip", "dataType"="int", "required"=false, "description"="skip first x tags, default is 0"},
     *      {"name"="areaCode", "dataType"="string", "required"=false, "description"="filter with area code, will not support after version 1.0"},
     *      {"name"="areaCodes[]", "dataType"="string", "required"=false, "description"="filter by multiple area codes with 'OR' operator , available at version 1.0"},
     *  }
     * )
     * @Route("/", name="api_managedTag")
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function getManagedTagsAction(Request $request)
    {
        $versionNum = $this->getVersionNum($request);
        if ($versionNum < 1.0)
        {
            return $this->getManagedTagsV0($request);
        }
        else
        {
            return $this->getManagedTagsV1($request);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    private function getManagedTagsV1(Request $request)
    {
        $limit = intval($request->get("limit"));
        if ($limit <= 0)
        {
            $limit = 25;
        }
        $skip = intval($request->get("skip"));
        if ($skip <= 0)
        {
            $skip = 0;
        }
        $areaCodes = $request->get('areaCodes');
        return new Response($this->createTagsQueryBuilderV1($limit, $skip, $areaCodes, $request));
    }

    /**
     * @param int $limit
     * @param int $skip
     * @param array $areaCodes
     * @param Request $request
     * @return string
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    private function createTagsQueryBuilderV1($limit, $skip, $areaCodes, Request $request)
    {
        $qb = $this->getManagedTagRepo()->getFindAllQueryBuilder();
        $qb->limit($limit)->skip($skip);
        $managedTags = $qb->getQuery()->execute();

        $data = array();

        foreach ($managedTags as $managedTag)
        {
            if ($managedTag instanceof ManagedTag)
            {
                $count = $this->getPostRepo()->queryCountOfTagedPost($managedTag->getKey(), $areaCodes);
                $data[] = array("tag" => $managedTag, "count" => $count);
            }
        }
        $serialize =  $this->serialize($request, $data, "display");
        return $this->OutputFormat($request,$serialize);
    }

    /**
     * @param Request $request
     * @return Response
     * @deprecated
     */
    private function getManagedTagsV0(Request $request)
    {
        $limit = intval($request->get("limit"));
        if ($limit <= 0)
        {
            $limit = 25;
        }
        $skip = intval($request->get("skip"));
        if ($skip <= 0)
        {
            $skip = 0;
        }
        $areaCode = $request->get('areaCode');

        return new Response($this->createTagsQueryBuilder($limit, $skip, $areaCode, $request));
    }

    /**
     * @param int $limit
     * @param int $skip
     * @param string $areaCode
     * @param Request $request
     * @return string
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    private function createTagsQueryBuilder($limit, $skip, $areaCode, Request $request)
    {
        $qb = $this->getManagedTagRepo()->getFindAllQueryBuilder();
        $qb->limit($limit)->skip($skip);
        $managedTags = $qb->getQuery()->execute();

        $data = array();

        foreach ($managedTags as $managedTag)
        {
            if ($managedTag instanceof ManagedTag)
            {
                if (!empty($areaCode))
                {
                    $areaCodes = array($areaCode);
                }
                else
                {
                    $areaCodes = array();
                }
                $count = $this->getPostRepo()->queryCountOfTagedPost($managedTag->getKey(), $areaCodes);
                $data[] = array("tag" => $managedTag, "count" => $count);
            }
        }
        $serialize =  $this->serialize($request, $data, "display");
        return $this->OutputFormat($request,$serialize);
    }
    /**
     * @param Request $request
     * @param string $serialize
     * @return string
     */
    public function OutputFormat($request ,$serialize)
    {
        $isProto = $request->get('isProto');
        if (!isset($isProto)){
            $isProto = false;
        }
        if(!$isProto){
            return $serialize;
        }else{
            $arr = json_decode($serialize,true);

            return base64_encode(TagWithCountDataProto::fromArray($arr)->toStream());
        }
    }
}

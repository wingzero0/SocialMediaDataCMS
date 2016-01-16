<?php
/**
 * User: kit
 * Date: 16/01/16
 * Time: 7:29 PM
 */

namespace CodingGuys\ApiBundle\Controller;


use AppBundle\Controller\AppBaseController;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\ODM\MongoDB\Query\Builder;
use Symfony\Component\HttpFoundation\Response;

/**
 * Api Ads controller.
 *
 * @Route("/ads")
 */
class SpotlightAdsController extends AppBaseController{
    /**
     * @ApiDoc(
     *  description="query ads",
     *  parameters={
     *      {"name"="limit", "dataType"="int", "required"=false, "description"="return x ads, default is 25"},
     *      {"name"="skip", "dataType"="int", "required"=false, "description"="skip first x ads, default is 0"},
     *  }
     * )
     * @Route("/", name="api_homepage_ads")
     * @Method("GET")
     */
    public function indexAction(Request $request){
        $qb = $this->createQueryBuilder($request);
        $ads = $qb->getQuery()->execute();
        $ret = array();
        foreach($ads as $ad){
            $ret[] = $ad;
        }
        $serialize = $this->serialize($ret, "display");
        return new Response($serialize);
    }
    /**
     * @param Request $request
     * @return Builder
     */
    private function createQueryBuilder(Request $request){
        $qb = $this->getSpotlightAdsRepo()->getFindAllQueryBuilder();
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
}
<?php
/**
 * User: kit
 * Date: 17/01/16
 * Time: 5:32 PM
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

class UtilityController extends AppBaseController{
    /**
     * @ApiDoc(
     *  description="query last update time",
     * )
     * @Route("/lastUpdateTime", name="api_last_update_time")
     * @Method("GET")
     */
    public function getLastUpdateTimeAction(Request $request){
        $ret = $this->getLogRecordRepo()->findLastPostReportLogRecord();
        $serialize = $this->serialize($ret->getLogTime()->format(\DateTime::ISO8601), "display");
        return new Response($serialize);
    }
    /**
     * @ApiDoc(
     *  description="query supported area code",
     * )
     * @Route("/areaCode", name="api_area_code")
     * @Method("GET")
     */
    public function getAreaCodeAction(Request $request){
        $ret = array(
            array('key' => 'hk', 'name_chi' => '香港', 'name_eng' => 'Hong Kong'),
            array('key' => 'mo', 'name_chi' => '澳門', 'name_eng' => 'Macau'),
        );
        return new Response($this->serialize($ret, "display"));
    }
    /**
     * @ApiDoc(
     *  description="test header versioning",
     * )
     * @Route("/testVersion", name="api_area_code")
     * @Method("GET")
     */
    public function testVersioningAction(Request $request){
        $ret = array(
            array('key' => 'v'.$this->getVersionNum() ),
        );
        return new Response($this->serialize($ret, "display"));
    }
    /**
     * @ApiDoc(
     *  description="output current supported api version",
     * )
     * @Route("/version", name="api_area_code")
     * @Method("GET")
     */
    public function getVersionAction(Request $request){
        $ret = array(
            array('version' => 'application/json;version=1.0', 'description' => "api version 1.0"),
            array('version' => 'application/json;', 'description' => "deprecated api. If not specific any accept header, system will handle request with this version"),
        );
        return new Response($this->serialize($ret, "display"));
    }
}
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
        $ret = array('hk', 'mo');
        return new Response($this->serialize($ret, "display"));
    }
}
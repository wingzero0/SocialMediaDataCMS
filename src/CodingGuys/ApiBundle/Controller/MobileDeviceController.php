<?php
/**
 * User: kit
 * Date: 22-Jan-16
 * Time: 10:26 AM
 */

namespace CodingGuys\ApiBundle\Controller;


use AppBundle\Controller\AppBaseController;
use AppBundle\Document\DeviceInfo;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Api ManagedTag controller.
 *
 * @Route("/device")
 */
class MobileDeviceController extends AppBaseController{
    /**
     * @ApiDoc(
     *  description="new device info and upload push token to server, think about the multi device saturation, each device id(never change) equals a token(will change), and it binding with one account, so many to one relationship",
     *  parameters={
     *      {"name"="androidOriOS", "dataType"="string", "required"=true, "description"="android or ios device"},
     *      {"name"="osVersion", "dataType"="string", "required"=false, "description"="os version"},
     *      {"name"="model", "dataType"="string", "required"=false, "description"="phone model"},
     *      {"name"="registrationId", "dataType"="string", "required"=true, "description"="push token"},
     *      {"name"="imei", "dataType"="string", "required"=false, "description"="android imei"},
     *  }
     * )
     * @Route("/")
     * @Method("POST")
     */
    public
    function NewDeviceAction(Request $request)
    {
        //$user = $this->container->get('security.context')->getToken()->getUser();

        $dm = $this->getDM();

        $registrationId = $request->get("registrationId");
        $AndroidOriOS = $request->get("androidOriOS");
        $osVersion = $request->get("osVersion");
        $model = $request->get("model");
        $imei = $request->get("imei");

        if ($registrationId == "NoSupportPushDevice") {
            $device = new DeviceInfo();
            $device->setAndroidOriOS($AndroidOriOS);
            $device->setPushEnabled(false);
            $device->setLastUpdateTime(new \MongoDate());
            $device->setRegisterTime(new \MongoDate());
            $device->setPhoneOsVersion($osVersion);
            $device->setRegistrationId($registrationId);
            $device->setPhoneModel($model);
            //$device->setUser($user);

            if ($imei)
                $device->setIMEI($imei);

            $dm->persist($device);
            $dm->flush();
            return new JsonResponse(array("id" => $device->getId(), "success" => true));

        }

        $device = $this->getDeviceInfoRepo()->findOneByRegistrationId($registrationId);

        if (!$device && $AndroidOriOS && $AndroidOriOS == "android" && $imei) {
            $device = $this->getDeviceInfoRepo()->findOneByIMEI($imei);
        }

        if (!$device) {
            $device = new DeviceInfo();
            $device->setAndroidOriOS($AndroidOriOS);
            $device->setPushEnabled(true);
            $device->setLastUpdateTime(new \MongoDate());
            $device->setRegisterTime(new \MongoDate());
            $device->setPhoneOsVersion($osVersion);
            $device->setRegistrationId($registrationId);
            $device->setPhoneModel($model);
            //$device->setUser($user);

            if ($imei)
                $device->setIMEI($imei);


        } else {
            $device->setPushEnabled(true);
            $device->setLastUpdateTime(new \MongoDate());
            $device->setPhoneOsVersion($osVersion);
            $device->setPhoneModel($model);
            //$device->setUser($user);


        }
        $dm->persist($device);
        $dm->flush();

        return new JsonResponse(array("id" => $device->getId(), "success" => true));

    }
}
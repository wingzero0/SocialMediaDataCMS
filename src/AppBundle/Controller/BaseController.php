<?php
/**
 * User: kit
 * Date: 02/07/15
 * Time: 20:10
 */

namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\Serializer\SerializationContext;
use Doctrine\ODM\MongoDB\DocumentManager;


class BaseController extends Controller{
    protected $mnemonoBizQueryPath = "AppBundle:MnemonoBiz";
    protected $postQueryPath = "AppBundle:PostBiz";
    protected $commentQueryPath = "AppBundle:Comment";
    /**
     * @param array $objs
     * @param string $version
     * @return string JSON string
     */
    protected function serialize($objs, $version){
        $data = array('data' => $objs);
        $serializer = $this->get('jms_serializer');
        return $serializer->serialize($data, 'json', SerializationContext::create()->setVersion($version));
    }

    /**
     * @return null|DocumentManager
     */
    protected function getDM(){
        return $this->get("doctrine_mongodb")->getManager();
    }
}
<?php
/**
 * Date: 7/1/2015
 * Time: 11:29 AM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/api/v{vNumber}")
 */
class ApiController extends Controller{
    /**
     * @Route("/", name="versioning")
     */
    public function indexAction(Request $request, $vNumber)
    {
        return new Response(json_encode(array('apiVersion' => $vNumber)));
    }
    /**
     * @Route("/{bizName}", name="retrival")
     */
    public function getBizAction(Request $request, $vNumber, $bizName)
    {
        $bizs = $this->get('doctrine_mongodb')
            ->getManager()
            ->createQueryBuilder('AppBundle:MnemonoBiz')
            ->getQuery()
            ->execute();

        return new Response($this->serialize(iterator_to_array($bizs, false), $vNumber));
    }

    /**
     * @param array $objs
     * @param string $version
     * @return string JSON string
     */
    private function serialize($objs, $version){
        $data = array('data' => $objs);
        $serializer = $this->get('jms_serializer');
        //return $serializer->serialize($objs, 'json', SerializationContext::create()->setVersion($version));
        return $serializer->serialize($data, 'json', SerializationContext::create()->setVersion($version));
    }
}
<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/biz", name="biz")
     */
    public function getBizAction(){
    	$bizs = $this->get('doctrine_mongodb')
	        ->getManager()
    		->createQueryBuilder('AppBundle:MnemonoBiz')
    		->getQuery()
    		->execute();

    	return new Response($this->serialize(iterator_to_array($bizs, false)));
    }

    /**
     * @param array $objs
     * @return string JSON string
     */
    private function serialize($objs){
        $data = array('data' => $objs);
        $serializer = $this->get('jms_serializer');
        return $serializer->serialize($data, 'json');
    }
}

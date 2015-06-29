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

	    $ret = array();
	    foreach($bizs as $biz){
	    	$ret[] = $biz;
	    }
    	return new Response(json_encode(array("ret" => $ret)));;
    }
}

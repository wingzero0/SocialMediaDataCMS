<?php
/**
 * User: kit
 * Date: 04/07/15
 * Time: 16:10
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CMSBaseController extends Controller{
    protected function retError($responseType, $message){
        $retData = array("ret" => "0", "message" => $message);
        if ($responseType == "json"){
            return new JsonResponse($retData);
        }else{
            return $this->render('AppBundle:Default:404.html.twig', $retData);
        }
    }
}
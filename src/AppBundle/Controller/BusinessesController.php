<?php
/**
 * Date: 7/1/2015
 * Time: 11:29 AM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\BaseController;

/**
 * @Route("/api/v{vNumber}")
 */
class BusinessesController extends BaseController{
    /**
     * @Route("/", name="versioning")
     */
    public function indexAction(Request $request, $vNumber)
    {
        return new Response(json_encode(array('apiVersion' => $vNumber)));
    }
    /**
     * @Route("/businesses/{bizName}", name="retrival_biz")
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
}
<?php
/**
 * Date: 7/1/2015
 * Time: 11:29 AM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @Route("/businesses", name="retrieve_biz")
     * @Method("GET")
     */
    public function getBizAction(Request $request, $vNumber)
    {
        $idGt = $request->get("idGt", null);
        $dm = $this->getDM();
        $qb = $dm->createQueryBuilder('AppBundle:MnemonoBiz');
        if ($idGt){
            $qb->field("id")->gt($idGt);
        }
        $bizs = $qb->limit(25)
            ->getQuery()
            ->execute();

        return new Response($this->serialize(iterator_to_array($bizs, false), $vNumber));
    }
    /**
     * @Route("/businesses/{id}", name="retrieve_biz_by_id")
     * @Method("GET")
     */
    public function getBizByIdAction(Request $request, $vNumber, $id)
    {
        $dm = $this->getDM();
        $bizs = $dm->createQueryBuilder('AppBundle:MnemonoBiz')
            ->field("id")->equals($id)
            ->getQuery()
            ->execute();

        return new Response($this->serialize(iterator_to_array($bizs, false), $vNumber));
    }
}
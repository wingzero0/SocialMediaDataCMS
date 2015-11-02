<?php
/**
 * User: kit
 * Date: 29/10/15
 * Time: 8:16 PM
 */

namespace CodingGuys\CMSBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use CodingGuys\CMSBundle\Controller\CMSBaseController;

/**
 * @Route("/dashboard/report")
 */
class PostReportController extends CMSBaseController{
    /**
     * find the lasted batch number
     *
     * @Route("/", name="post_report_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request){
        $batchNum = $this->getPostForReviewRepo()->findLastBatchNum();
        return $this->redirect($this->generateUrl("post_report_batch", array("batchNum" => $batchNum)));
    }
    /**
     * Displays all post in batch
     *
     * @Route("/{batchNum}", name="post_report_batch")
     * @Method("GET")
     * @Template()
     */
    public function showBatchAction(Request $request, $batchNum){
        $limit = 50;
        $page = intval($request->get('page', 1));
        $qb = $this->getPostForReviewRepo()->getQueryBuilderFindByBatch(intval($batchNum));

        $query = $qb->limit(100)->sort(
            array("rankPosition" => "asc", "rankScore" => "desc")
        )->getQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            $limit
        );

        return array(
            'pagination' => $pagination,
        );
    }
}
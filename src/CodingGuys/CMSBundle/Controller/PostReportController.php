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
     * Displays all the batch number in DB
     *
     * @Route("/", name="post_report_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request){
        return array("test" => 'it works');
    }
    /**
     * Displays all post in batch
     *
     * @Route("/{batchId}", name="post_report_batch")
     * @Method("GET")
     * @Template()
     */
    public function showBatchAction(Request $request, $batchId){
        $qb = $this->getPostForReviewRepo()->getQueryBuilderFindByBatch(intval($batchId));
        $postForReview = $qb->limit(100)->sort("rankPosition")->getQuery()->execute();
        return array("postForReview" => $postForReview, "count" => $postForReview->count());
    }
}
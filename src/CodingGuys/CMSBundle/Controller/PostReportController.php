<?php
/**
 * User: kit
 * Date: 29/10/15
 * Time: 8:16 PM
 */

namespace CodingGuys\CMSBundle\Controller;

use Doctrine\ODM\MongoDB\Query\Builder;
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
     * show post by rank
     *
     * @Route("/", name="post_report_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request){
        $limit = 50;
        $page = intval($request->get('page', 1));
        $qb = $this->getPostRepo()->getQueryBuilderSortWithRank();
        $qb = $this->compileFilter($request, $qb);
        $paginator  = $this->getPaginator();
        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );

        return array(
            'pagination' => $pagination,
        );
    }

    /**
     * @param Request $request
     * @param Builder $qb
     * @return Builder
     */
    private function compileFilter(Request $request, Builder $qb){
        $publishStatus = $request->get('publishStatus');
        if (!empty($publishStatus)){
            $qb->field('publishStatus')->equals($publishStatus);
        }
        $spotlight = $request->get('spotlight');
        if (!empty($spotlight)){
            if ($spotlight == 'Y'){
                $qb->field('spotlight')->equals(true);
            }else{
                $qb->field('spotlight')->notEqual(true);
            }
        }
        return $qb;
    }
}
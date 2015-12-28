<?php
/**
 * User: kit
 * Date: 29/10/15
 * Time: 8:16 PM
 */

namespace CodingGuys\CMSBundle\Controller;

use AppBundle\Controller\AppBaseController;
use Doctrine\ODM\MongoDB\Query\Builder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\Post;

/**
 * @Route("/dashboard/report")
 */
class PostReportController extends AppBaseController{
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
        $paginator  = $this->getKnpPaginator();
        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );

        return array(
            'pagination' => $pagination,
            'page' => $page,
            'limit' => $limit,
            'lovPublishStatus' => Post::listOfPublishStatus(),
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
        $tagString = $request->get('tag');
        if (!empty($tagString)){
            $tags = preg_split('/,/', $tagString);
            foreach($tags as $tag){
                $tagTrim = trim($tag);
                if (!empty($tagTrim)){
                    $qb->addAnd(
                        $qb->expr()->field('tags')->equals($tagTrim)
                    );
                }
            }
        }
        $rank = intval($request->get('rank', "-1"));
        if ($rank >= 0){
            $qb->field("rankPosition")->equals($rank);
        }
        $interval = intval($request->get("interval"));
        $this->getLogger()->info("interval");
        if (!empty($interval)){
            $this->getLogger()->info($interval);
            $nowDate = new \DateTime();
            $createDate = $nowDate->sub(new \DateInterval("P". $interval ."D"));
            $qb->field("createAt")->gte($createDate);
        }
        return $qb;
    }
}
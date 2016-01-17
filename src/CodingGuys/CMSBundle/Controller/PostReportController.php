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

        $endDatePlaceHolder = new \DateTime();
        $startDatePlaceHolder = $endDatePlaceHolder->sub(new \DateInterval("P7D"));

        return array(
            'pagination' => $pagination,
            'page' => $page,
            'limit' => $limit,
            'lovPublishStatus' => Post::listOfPublishStatus(),
            'startDatePlaceHolder' => $startDatePlaceHolder->format(\DateTime::ISO8601),
            'endDatePlaceHolder' => $endDatePlaceHolder->format(\DateTime::ISO8601),
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
        $showAtHomepage = $request->get('showAtHomepage');
        if (!empty($showAtHomepage)){
            if ($showAtHomepage == 'Y'){
                $qb->field('showAtHomepage')->equals(true);
            }else{
                $qb->field('showAtHomepage')->notEqual(true);
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

        $qb = $this->parseStartEndDate($qb, $request);

        $searchField = $request->get('search');
        if (!empty($searchField)){
            $regexArray = $this->getKeywordRegex($searchField);
            $qb->field('content')->all($regexArray);
        }
        return $qb;
    }

    /**
     * @param string $searchField
     * @return array
     */
    private function getKeywordRegex($searchField){
        $keywords = explode(',', $searchField);
        $regexArray = array();
        foreach($keywords as $keyword){
            $keyword = '/' . $keyword . '/i';
            $regexArray[] = new \MongoRegex($keyword);
        }
        return $regexArray;
    }

    private function parseStartEndDate(Builder $qb, Request $request){
        $startDateStr = $request->get("startDate");
        $this->getLogger()->info("startDate");

        if (!empty($startDateStr)){
            $this->getLogger()->info($startDateStr);
            $startDate = \DateTime::createFromFormat(\DateTime::ISO8601, $startDateStr);
            if ($startDate instanceof \DateTime){
                $qb->addAnd(
                    $qb->expr()->field("createAt")->gte($startDate)
                );
            }
        }

        $endDateStr = $request->get("endDate");
        $this->getLogger()->info("endDate");
        if (!empty($endDateStr)){
            $this->getLogger()->info($endDateStr);
            $endDate = \DateTime::createFromFormat(\DateTime::ISO8601, $endDateStr);
            if ($endDate instanceof \DateTime){
                $qb->addAnd(
                    $qb->expr()->field("createAt")->lte($endDate)
                );
            }
        }
        return $qb;
    }
}
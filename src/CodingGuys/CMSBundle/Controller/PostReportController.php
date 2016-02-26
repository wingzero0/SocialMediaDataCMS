<?php
/**
 * User: kit
 * Date: 29/10/15
 * Time: 8:16 PM
 */

namespace CodingGuys\CMSBundle\Controller;

use AppBundle\Controller\AppBaseController;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\Utility\LogRecord;
use AppBundle\Utility\GearmanServiceName;
use Doctrine\ODM\MongoDB\Query\Builder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\Post;
use AppBundle\Utility\LoopCollectionStrategy;

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
            'lastUpdateTime' => $this->getLastUpdateTime(),
        );
    }
    /**
     * re rank all post
     *
     * @Route("/rerank", name="post_report_rerank")
     * @Method("GET")
     * @Template()
     */
    public function reRankAction(Request $request){
        if ($request->get("rerank") == "1"){
            $this->callBackgroundReviewJob();
            return $this->redirect($this->generateUrl("post_report_home"));
        }
        return array();
    }

    private function callBackgroundReviewJob(){
        $json = json_encode(array("id" => null));
        $this->getGearman()->doBackgroundJob(GearmanServiceName::$postReviewRankJob, $json);

        $loopS = new LoopCollectionStrategy();
        $loopS->loopCollectionWithSkipParam(function($limit, $skip){
            return $this->getMnemenoBizRepo()->getQueryBuilderFindAll($limit, $skip);
        }, function(MnemonoBiz $biz){
            $json = json_encode(array("id" => $biz->getId()));
            $this->getGearman()->doBackgroundJob(GearmanServiceName::$postReviewRankJob, $json);
        }, function(){
            // to nothing, don't want to reset the dm
        });
    }


    /**
     * @return String
     */
    private function getLastUpdateTime(){
        $logRecord = $this->getLogRecordRepo()->findLastPostReportLogRecord();
        if ($logRecord instanceof LogRecord){
            return $logRecord->getLogTime()->format(\DateTime::ISO8601);
        }else{
            return "No record";
        }
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
        $tagsString = $request->get('tags');
        $tags = $this->splitStrByComma($tagsString);
        foreach($tags as $tag){
            $qb->addAnd(
                $qb->expr()->field('tags')->equals($tag)
            );
        }

        $citiesString = $request->get('cities');
        $cities = $this->splitStrByComma($citiesString);
        if (!empty($cities)){
            $qb->field("cities")->in($cities);
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
     * @param $sourceStr
     * @return array
     */
    private function splitStrByComma($sourceStr){
        $ret = array();
        if (!empty($sourceStr)){
            $splitedStr = preg_split('/,/', $sourceStr);
            foreach($splitedStr as $str){
                $trimStr = trim($str);
                if (!empty($trimStr)){
                    $ret[] = $trimStr;
                }
            }
        }
        return $ret;
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
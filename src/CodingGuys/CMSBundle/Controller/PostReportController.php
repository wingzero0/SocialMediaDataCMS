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
use MongoDB\BSON\Regex;

/**
 * @Route("/dashboard/report")
 */
class PostReportController extends AppBaseController
{

    private $dateFormat = 'Y-m-d H:i';
    /**
     * show post by rank
     *
     * @Route("/", name="post_report_home")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $limit = 50;
        $page = intval($request->get('page', 1));
        $qb = $this->getPostRepo()->getQueryBuilderSortWithRank();
        $filterParams = $this->getFilterParams($request);
        $qb = $this->compileFilter($filterParams, $qb);
        $paginator = $this->getKnpPaginator();
        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
        $this->getPostRepo()
            ->primeReferences($pagination, ['mnemonoBiz']);
        $endDatePlaceHolder = new \DateTime();
        $startDatePlaceHolder = clone $endDatePlaceHolder;
        $startDatePlaceHolder->sub(new \DateInterval("P7D"));

        return array(
            'pagination' => $pagination,
            'page' => $page,
            'limit' => $limit,
            'lovPublishStatus' => Post::listOfPublishStatus(),
            'startDatePlaceHolder' => $startDatePlaceHolder->format($this->dateFormat),
            'endDatePlaceHolder' => $endDatePlaceHolder->format($this->dateFormat),
            'lastUpdateTime' => $this->getLastUpdateTime(),
            'filterParams' => $filterParams,
        );
    }
    /**
     * re rank all post
     *
     * @Route("/rerank", name="post_report_rerank")
     * @Method("GET")
     * @Template()
     */
    public function reRankAction(Request $request)
    {
        if ($request->get("rerank") == "1")
        {
            $this->callBackgroundReviewJob();
            return $this->redirect($this->generateUrl("post_report_home"));
        }
        return array();
    }

    private function callBackgroundReviewJob()
    {
        $json = json_encode(array("id" => null));
        $this->getGearman()->doBackgroundJob(GearmanServiceName::$postReviewRankJob, $json);

        $loopS = new LoopCollectionStrategy();
        $loopS->loopCollectionWithSkipParam(function($limit, $skip) {
            return $this->getMnemenoBizRepo()->getQueryBuilderFindAll($limit, $skip);
        }, function(MnemonoBiz $biz) {
            $json = json_encode(array("id" => $biz->getId()));
            $this->getGearman()->doBackgroundJob(GearmanServiceName::$postReviewRankJob, $json);
        }, function() {
            // to nothing, don't want to reset the dm
        });
    }


    /**
     * @return string
     */
    private function getLastUpdateTime()
    {
        $logRecord = $this->getLogRecordRepo()->findLastPostReportLogRecord();
        if ($logRecord instanceof LogRecord)
        {
            return $logRecord->getLogTime()->format(\DateTime::ISO8601);
        }
        else
        {
            return null;
        }
    }

    /**
     * @param array $filterParams
     * @param Builder $qb
     * @return Builder
     */
    private function compileFilter(array $filterParams, Builder $qb)
    {
        $publishStatus = $filterParams['publishStatus'];
        if (!empty($publishStatus))
        {
            $qb->field('publishStatus')->equals($publishStatus);
        }
        $showAtHomepage = $filterParams['showAtHomepage'];
        if (!empty($showAtHomepage))
        {
            if ($showAtHomepage == 'Y')
            {
                $qb->field('showAtHomepage')->equals(true);
            }
            else
            {
                $qb->field('showAtHomepage')->notEqual(true);
            }
        }
        $tagsString = $filterParams['tags'];
        $tags = $this->splitStrByComma($tagsString);
        foreach ($tags as $tag)
        {
            $qb->addAnd(
                $qb->expr()->field('tags')->equals($tag)
            );
        }

        $citiesString = $filterParams['cities'];
        $cities = $this->splitStrByComma($citiesString);
        if (!empty($cities))
        {
            $qb->field("cities")->in($cities);
        }

        $rank = intval($filterParams['rank']);
        if ($rank > 0)
        {
            $qb->field("rankPosition")->equals($rank);
        }

        $qb = $this->parseStartEndDate($qb, $filterParams);

        $searchField = $filterParams['search'];
        if (!empty($searchField))
        {
            $regexArray = $this->getKeywordRegex($searchField);
            $qb->field('content')->all($regexArray);
        }
        return $qb;
    }

    /**
     * @param $sourceStr
     * @return array
     */
    private function splitStrByComma($sourceStr)
    {
        $ret = array();
        if (!empty($sourceStr))
        {
            $splitedStr = preg_split('/,/', $sourceStr);
            foreach($splitedStr as $str)
            {
                $trimStr = trim($str);
                if (!empty($trimStr))
                {
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
    private function getKeywordRegex($searchField)
    {
        $keywords = explode(',', $searchField);
        $regexArray = array();
        foreach ($keywords as $keyword)
        {
            $regexArray[] = new Regex(trim($keyword), 'i');
        }
        return $regexArray;
    }

    /**
     * @param Builder $qb
     * @param array $filterParams
     * @return Builder
     */
    private function parseStartEndDate(Builder $qb, array $filterParams)
    {
        $startDateStr = $filterParams["startDate"];
        $this->getLogger()->info("startDate");

        if (!empty($startDateStr))
        {
            $this->getLogger()->info($startDateStr);
            $startDate = \DateTime::createFromFormat($this->dateFormat, $startDateStr);
            if ($startDate instanceof \DateTime)
            {
                $qb->addAnd(
                    $qb->expr()->field("createAt")->gte($startDate)
                );
            }
        }

        $endDateStr = $filterParams["endDate"];
        $this->getLogger()->info("endDate");
        if (!empty($endDateStr))
        {
            $this->getLogger()->info($endDateStr);
            $endDate = \DateTime::createFromFormat($this->dateFormat, $endDateStr);
            if ($endDate instanceof \DateTime){
                $qb->addAnd(
                    $qb->expr()->field("createAt")->lte($endDate)
                );
            }
        }
        return $qb;
    }

    /**
     * Show active game posts
     *
     * @Route("/game", name="post_report_active_game")
     * @Method("GET")
     * @Template()
     */
    public function gameAction(Request $request)
    {
        $limit = 200;
        $page = intval($request->get('page', 1));
        $query = $this->getPostRepo()->getActiveGamePostsQuery();
        $paginator = $this->getKnpPaginator();
        $posts = $paginator->paginate($query, $page, $limit);
        $this->getPostRepo()
            ->primeReferences($posts, ['mnemonoBiz']);
        return [
            'posts' => $posts,
        ];
    }

    /**
     *
     * @param Request $request
     * @return array
     *
     */
    private function getFilterParams(Request $request)
    {
        return [
            'tags' => trim($request->get('tags', '')),
            'cities' => trim($request->get('cities', '')),
            'rank' => trim($request->get('rank', '')),
            'search' => trim($request->get('search', '')),
            'publishStatus' => trim($request->get('publishStatus', '')),
            'showAtHomepage' => trim($request->get('showAtHomepage', '')),
            'startDate' => trim(preg_replace('/(\s+)/', ' ', $request->get('startDate', ''))),
            'endDate' => trim(preg_replace('/(\s+)/', ' ', $request->get('endDate', ''))),
        ];
    }
}

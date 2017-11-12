<?php
/**
 * User: kit
 * Date: 25/12/15
 * Time: 4:06 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services;

use AppBundle\Document\Post;
use AppBundle\Document\Utility\LogRecord;
use AppBundle\Utility\GearmanServiceName;
use Mnemono\BackgroundServiceBundle\Services\BaseService;
use AppBundle\Document\MnemonoBiz;
use Mmoreram\GearmanBundle\Driver\Gearman;

/**
 * @Gearman\Work(
 *     iterations = 1000,
 *     description = "calculate rank of the post in the biz",
 *     defaultMethod = "doBackground",
 *     service="PostReviewService"
 * )
 */
class PostReviewService extends BaseService
{
    private $endDate;
    private $startDate;

    /**
     * Job for create post form fbID
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     iterations = 1000,
     *     name = "rank",
     *     description = "calculate rank of the post in the target biz"
     * )
     */
    public function rankPost(\GearmanJob $job)
    {
        try
        {
            $key_json = json_decode($job->workload(), true);
            $this->resetDM();
            $this->resetDateRange();
            $biz = $this->getMnemenoBizRepo()->find($key_json["id"]);
            if (!$biz instanceof MnemonoBiz)
            {
                $biz = null;
            }
            $updatedPosts = $this->getUpdatedPosts($biz);

            $i = 1;
            $dm = $this->getDM();
            foreach ($updatedPosts as $post)
            {
                if ($post instanceof Post)
                {
                    if ($biz == null)
                    {
                        $post->setRankPosition(1);
                    }
                    else
                    {
                        $post->setRankPosition($i);
                    }
                    $post->setPublishStatus(Post::STATUS_PUBLISHED);
                    if ($i == 1)
                    {
                        $post->setShowAtHomepage(true);
                    }
                    $dm->persist($post);
                    $i++;
                }
            }
            $this->writeLogRecord();
            $dm->flush();
            return true;
        }
        catch (\Exception $e)
        {
            $this->logExecption($e);
            exit(-1);
        }
    }

    private function getUpdatedPosts(MnemonoBiz $biz = null)
    {
        $postCursor = $this->getPostQueryBuilder($biz, 0, 0)->getQuery()->execute();
        $updatedPosts = array();
        foreach ($postCursor as $post)
        {
            if ($post instanceof Post)
            {
                $updatedPost = $this->updatePostScore($post);
                if ($updatedPost instanceof Post)
                {
                    $updatedPosts[] = $updatedPost;
                }
            }
        }
        usort($updatedPosts, function(Post $a, Post $b)
        {
            if ($a->getFinalScore() < $b->getFinalScore())
            {
                return 1;
            }
            else if ($a->getFinalScore() > $b->getFinalScore())
            {
                return -1;
            }
            else
            {
                return 0;
            }
        });
        return $updatedPosts;
    }

    /**
     * @param Post $post
     * @return Post|null
     */
    private function updatePostScore(Post $post)
    {
        $id = $post->getId();
        $json = json_encode(array("id" => $post->getId()));
        $oldScore = $post->getFinalScore();
        $this->getGearman()->doNormalJob(GearmanServiceName::$postScoreUpdateJob, $json);
        $this->getDM()->detach($post);
        $post = $this->getPostRepo()->find($id);
        if ($post instanceof Post)
        {
            $newScore = $post->getFinalScore();
            $this->getLogger()->debug("post old score:" . $oldScore . " post new score:" . $newScore);
            return $post;
        }
        return null;
    }

    private function writeLogRecord()
    {
        $logRecord = new LogRecord();
        $logRecord->setCategory("postReview");
        $logRecord->setLogTime(new \DateTime());
        $this->getDM()->persist($logRecord);
    }

    /**
     * @param MnemonoBiz $biz
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    private function getPostQueryBuilder(MnemonoBiz $biz = null, $limit, $skip)
    {
        $postRepo = $this->getPostRepo();
        $qb = null;
        if ($biz == null)
        {
            $qb = $postRepo->getQueryBuilderFindNonExpireWithBizNotExisit($this->endDate, $limit, $skip);
        }
        else
        {
            $qb = $postRepo->getQueryBuilderFindNonExpireByBiz($biz, $this->endDate, $limit, $skip);
        }
        return $qb;
    }

    /**
     * @return int
     */
    private function getDateRangeParameter()
    {
        return 3;
    }

    private function resetDateRange()
    {
        $dateRangeParameter = $this->getDateRangeParameter();
        $this->endDate = new \DateTime();
        $this->startDate = clone($this->endDate);
        $this->startDate->sub(new \DateInterval('P'.$dateRangeParameter."D"));
    }
}

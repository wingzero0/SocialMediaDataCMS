<?php
/**
 * User: kit
 * Date: 12/7/2015
 * Time: 4:51 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services;

use AppBundle\Document\Weibo\WeiboFeed;
use Mmoreram\GearmanBundle\Driver\Gearman;
use AppBundle\Document\Post;
use AppBundle\Document\Facebook\FacebookFeed;
use Mnemono\BackgroundServiceBundle\Services\Score\FbScore;
use Mnemono\BackgroundServiceBundle\Services\Score\WeiboScore;

/**
 * @Gearman\Work(
 *     iterations = 1000,
 *     description = "calculate post score",
 *     defaultMethod = "doBackground",
 *     service="PostScoreService"
 * )
 */
class PostScoreService extends BaseService
{
    /**
     * Job for create post form fbID
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     iterations = 1000,
     *     name = "updateScore",
     *     description = "Create post"
     * )
     */
    public function updateScore(\GearmanJob $job)
    {
        try
        {
            $key_json = json_decode($job->workload(), true);
            $id = $key_json["id"];
            $this->resetDM();
            $post = $this->getPostRepo()->find($id);
            $this->updatePostLocalScore($post);
            $this->updatePostFinalScore($post);
            return true;
        }
        catch (\Exception $e)
        {
            $this->logExecption($e);
            exit(-1);
        }
    }

    /**
     * as a step stone to get FbFeedTimestampRepo
     * @return \AppBundle\Repository\Facebook\FacebookFeedTimestampRepository
     */
    public function proxyFbFeedTimestampRepo()
    {
        return $this->getFbFeedTimestampRepo();
    }

    /**
     * @param \DateTime $dateTime1
     * @param \DateTime $dateTime2
     * @return float
     */
    public function timePenaltyFactor(\DateTime $dateTime1, \DateTime $dateTime2)
    {
        $interval = $dateTime1->diff($dateTime2);
        $gravity = $this->getWeighting("gravity");
        if ($interval->days < 1)
        {
            return 1.0 / pow(0.5, $gravity);
        }
        return 1.0 / pow($interval->days, $gravity);
    }

    /**
     * @param $key
     * @return float
     */
    public function getWeighting($key)
    {
        $weighting = $this->getWeightingRepo()->findOneByName($key);
        if ($weighting == null)
        {
            return 1.0;
        }
        return $weighting->getValue();
    }

    private function updatePostFinalScore(Post $post)
    {
        $localWeight = $this->getWeighting("localWeight");
        $adminWeight = $this->getWeighting("adminWeight");

        $finalScore = $post->updateFinalScore($localWeight, $adminWeight);
        $this->persistPost($post);
        return $finalScore;
    }

    /**
     * @param Post $post
     * @return float|int
     */
    private function updatePostLocalScore(Post $post)
    {
        $score = $this->calLocalScoreBySource($post);
        $score = $score * $this->timePenaltyFactor(new \DateTime(), $post->getCreateAt());
        $post->setLocalScore($score);
        $this->persistPost($post);
        return $score;
    }

    /**
     * @param Post $post
     * @return float
     */
    private function calLocalScoreBySource(Post $post)
    {
        $feed = $post->getImportFromRef();
        $scoreFactory = null;
        if ($feed instanceof FacebookFeed)
        {
            $scoreFactory = new FbScore($this);
        }
        else if ($feed instanceof WeiboFeed)
        {
            $scoreFactory = new WeiboScore($this);
        }
        if ($scoreFactory != null)
        {
            return $scoreFactory->calLocalScore($post);
        }
        return 0.0;
    }

    /**
     * @param Post $post
     */
    private function persistPost(Post $post)
    {
        $dm = $this->getDM();
        $dm->persist($post);
        $dm->flush();
    }
}

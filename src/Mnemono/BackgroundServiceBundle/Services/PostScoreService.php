<?php
/**
 * User: kit
 * Date: 12/7/2015
 * Time: 4:51 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services;

use Mnemono\BackgroundServiceBundle\Services\BaseService;
use Mmoreram\GearmanBundle\Driver\Gearman;
use AppBundle\Document\Post;
use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\Facebook\FacebookFeedTimestamp;

/**
 * @Gearman\Work(
 *     iterations = 1000,
 *     description = "calculate post score",
 *     defaultMethod = "doBackground",
 *     service="PostScoreService"
 * )
 */
class PostScoreService extends BaseService{
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
    public function updateScore(\GearmanJob $job){
        try{
            $key_json = json_decode($job->workload(), true);
            $id = $key_json["id"];
            $this->resetDM();
            $post = $this->getPostRepo()->find($id);
            $this->updatePostLocalScore($post);
            $this->updatePostFinalScore($post);
            return true;
        }catch (\Exception $e){
            $this->logExecption($e);
            exit(-1);
        }
    }

    private function updatePostFinalScore(Post $post){
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
    private function updatePostLocalScore(Post $post){
        $fbFeed = $post->getImportFromRef();
        $score = 0.0;
        if ($fbFeed instanceof FacebookFeed){
            $feedTimestamps = $this->getFbFeedTimestampRepo()->findAllByFeed($fbFeed, 12);
            $likes = array();
            $comments = array();
            $updateTimeArray = array();
            foreach($feedTimestamps as $feedTimestamp){
                if ($feedTimestamp instanceof FacebookFeedTimestamp){
                    $likes[] = $feedTimestamp->getLikesTotalCount();
                    $comments[] = $feedTimestamp->getCommentsTotalCount();
                    $updateTimeArray[] = $feedTimestamp->getUpdateTime();
                }
            }
            $deltaLikeScore = 0;
            $deltaCommentScore = 0;
            for ($i = 0;$i < count($likes) - 1; $i++){
                $timestamp1 = $updateTimeArray[$i];
                $timestamp2 = $updateTimeArray[$i + 1];
                $penaltyFactor = $this->timePenaltyFactor($timestamp1, $timestamp2);

                $deltaLikeScore += ($likes[$i] - $likes[$i + 1]) * $penaltyFactor;
                $deltaCommentScore += ($comments[$i] - $comments[$i+1]) * $penaltyFactor;
            }
            $score += ($deltaLikeScore * $this->getWeighting("deltaLikes")
                + $deltaCommentScore * $this->getWeighting("deltaComments"));
            if (!empty($likes)){
                $score += ($likes[0] * $this->getWeighting("totalLikes")
                    + $comments[0] * $this->getWeighting("totalComments"));
            }
        }

        $score = $score * $this->timePenaltyFactor(new \DateTime(), $post->getCreateAt());
        $post->setLocalScore($score);
        $this->persistPost($post);
        return $score;
    }

    private function timePenaltyFactor(\DateTime $dateTime1, \DateTime $dateTime2){
        $interval = $dateTime1->diff($dateTime2);
        $gravity = $this->getWeighting("gravity");
        if ($interval->days <= 1){
            return 1.0 / pow(1, $gravity);
        }else{
            return 1.0 / pow($interval->days, $gravity);
        }
    }

    /**
     * @param $key
     * @return float
     */
    private function getWeighting($key){
        $weighting = $this->getWeightingRepo()->findOneByName($key);
        if ($weighting == null){
            return 1.0;
        }
        return $weighting->getValue();
    }

    /**
     * @param Post $post
     */
    private function persistPost(Post $post){
        $dm = $this->getDM();
        $dm->persist($post);
        $dm->flush();
    }
}
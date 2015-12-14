<?php
/**
 * User: kit
 * Date: 12/7/2015
 * Time: 4:51 PM
 */

namespace AppBundle\Services;

use AppBundle\Services\BaseService;
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
        $key_json = json_decode($job->workload(), true);
        $id = $key_json["id"];
        $this->resetDM();
        $post = $this->getPostRepo()->find($id);
        $this->updatePostLocalScore($post);
        $this->updatePostFinalScore($post);
    }

    private function updatePostFinalScore(Post $post){
        $localWeight = $this->getWeighting("localWeight");
        $globalWeight = $this->getWeighting("globalWeight");
        $adminWeight = $this->getWeighting("adminWeight");

        $finalScore = $post->updateFinalScore($localWeight , $globalWeight , $adminWeight );
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
            $timestamps = $this->getFbFeedTimestampRepo()->findAllByFeed($fbFeed, 12);
            $likes = array();
            $comments = array();
            foreach($timestamps as $timestamp){
                if ($timestamp instanceof FacebookFeedTimestamp){
                    $likes[] = $timestamp->getLikesTotalCount();
                    $comments[] = $timestamp->getCommentsTotalCount();
                }
            }
            $deltaLikeScore = 0;
            $deltaCommentScore = 0;
            for ($i = 0;$i < count($likes) - 1; $i++){
                $deltaLikeScore += ($likes[$i] - $likes[$i + 1]);
                $deltaCommentScore += ($comments[$i] - $comments[$i+1]);
            }
            $score += ($deltaLikeScore * $this->getWeighting("deltaLikes")
                + $deltaCommentScore * $this->getWeighting("deltaComments"));
            if (!empty($likes)){
                $score += ($likes[0] * $this->getWeighting("totalLikes")
                    + $comments[0] * $this->getWeighting("totalComments"));
            }
        }

        $score = $score * $this->timePenaltyFactor($post);
        $post->setLocalScore($score);
        $this->persistPost($post);
        return $score;
    }

    private function timePenaltyFactor(Post $post){
        $createAt = $post->getCreateAt();
        $currentTime = new \DateTime();
        $interval = $currentTime->diff($createAt);
        if ($interval->days <= 1){
            print_r($interval);
            return 1.0 / log(1.5);
        }else{
            return 1.0 / log($interval->days);
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
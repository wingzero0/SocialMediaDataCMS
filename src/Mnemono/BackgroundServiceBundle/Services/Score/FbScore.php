<?php
/**
 * User: kit
 * Date: 03/04/16
 * Time: 8:54 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services\Score;

use AppBundle\Document\Post;
use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\Facebook\FacebookFeedTimestamp;
use Mnemono\BackgroundServiceBundle\Services\PostScoreService;

class FbScore implements CalScore{
    private $postService;
    public function __construct(PostScoreService $postService){
        $this->postService = $postService;
    }

    /**
     * @param Post $post
     * @return float
     */
    public function calLocalScore(Post $post){
        $fbFeed = $post->getImportFromRef();
        $score = 0.0;
        $postService = $this->postService;
        if ($fbFeed instanceof FacebookFeed
            && $postService instanceof PostScoreService){
            $feedTimestamps = $postService->proxyFbFeedTimestampRepo()->findAllByFeed($fbFeed, 12);
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
                $penaltyFactor = $postService->timePenaltyFactor($timestamp1, $timestamp2);

                $deltaLikeScore += ($likes[$i] - $likes[$i + 1]) * $penaltyFactor;
                $deltaCommentScore += ($comments[$i] - $comments[$i+1]) * $penaltyFactor;
            }
            $score += ($deltaLikeScore * $postService->getWeighting("deltaLikes")
                + $deltaCommentScore * $postService->getWeighting("deltaComments"));
            if (!empty($likes)){
                $score += ($likes[0] * $postService->getWeighting("totalLikes")
                    + $comments[0] * $postService->getWeighting("totalComments"));
            }
        }
        return $score;
    }

}
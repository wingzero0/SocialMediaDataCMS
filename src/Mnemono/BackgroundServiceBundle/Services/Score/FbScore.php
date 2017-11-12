<?php
/**
 * User: kit
 * Date: 03/04/16
 * Time: 8:54 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services\Score;

use AppBundle\Document\Post;
use Mnemono\BackgroundServiceBundle\Services\PostScoreService;

class FbScore implements CalScore
{
    private $postService;

    public function __construct(PostScoreService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @param Post $post
     * @return float
     */
    public function calLocalScore(Post $post)
    {
        $fbFeed = $post->getImportFromRef();
        $score = 0.0;
        $limit = 0;
        if (empty($fbFeed))
        {
            return $score;
        }
        $totalLikesWeight = $this->postService->getWeighting('totalLikes');
        $totalCommentsWeight = $this->postService->getWeighting('totalComments');
        if ($totalLikesWeight !== 0 || $totalCommentsWeight !== 0)
        {
            $limit = 1;
        }
        $deltaLikesWeight = $this->postService->getWeighting('deltaLikes');
        $deltaCommentsWeight = $this->postService->getWeighting('deltaComments');
        if ($deltaLikesWeight !== 0 || $deltaCommentsWeight !== 0)
        {
            $limit = 12;
        }
        if ($limit <= 0)
        {
            return $score;
        }
        $feedTimestamps = $this->postService->proxyFbFeedTimestampRepo()
            ->findAllByFeed($fbFeed, $limit);
        $likes = [];
        $comments = [];
        $updateTimeArray = [];
        foreach ($feedTimestamps as $feedTimestamp)
        {
            $likes[] = $feedTimestamp->getLikeTotalCount();
            $comments[] = $feedTimestamp->getCommentsTotalCount();
            $updateTimeArray[] = $feedTimestamp->getUpdateTime();
        }
        $deltaLikeScore = 0;
        $deltaCommentScore = 0;
        for ($i = 0; $i < count($likes) - 1; $i++)
        {
            $timestamp1 = $updateTimeArray[$i];
            $timestamp2 = $updateTimeArray[$i + 1];
            $factor = $this->postService
                ->timePenaltyFactor($timestamp1, $timestamp2);
            $deltaLikeScore += ($likes[$i] - $likes[$i + 1]) * $factor;
            $deltaCommentScore += ($comments[$i] - $comments[$i + 1]) * $factor;
        }
        $score += $deltaLikeScore * $deltaLikesWeight;
        $score += $deltaCommentScore * $deltaCommentsWeight;
        if (!empty($likes))
        {
            $score += $likes[0] * $totalLikesWeight;
        }
        if (!empty($comments))
        {
            $score += $comments[0] * $totalCommentsWeight;
        }
        return $score;
    }
}

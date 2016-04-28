<?php
/**
 * User: kit
 * Date: 03/04/16
 * Time: 9:48 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services\Score;

use AppBundle\Document\Weibo\WeiboFeed;
use Mnemono\BackgroundServiceBundle\Services\PostScoreService;
use AppBundle\Document\Post;

class WeiboScore implements CalScore{
    private $postService;
    public function __construct(PostScoreService $postService){
        $this->postService = $postService;
    }

    /**
     * @param Post $post
     * @return float
     */
    public function calLocalScore(Post $post){
        $feed = $post->getImportFromRef();
        $postService = $this->postService;
        $score = 0.0;
        if ($feed instanceof WeiboFeed
            && $postService instanceof PostScoreService){
            $score = floatval($feed->getCommentsCount() + $feed->getLikeCount());
        }
        return $score;
    }
}
<?php
/**
 * User: kit
 * Date: 03/04/16
 * Time: 9:58 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services\Score;
use AppBundle\Document\Post;

interface CalScore {
    /**
     * @param Post $post
     * @return float
     */
    public function calLocalScore(Post $post);
}
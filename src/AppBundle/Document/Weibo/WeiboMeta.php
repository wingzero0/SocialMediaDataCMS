<?php
/**
 * User: kit
 * Date: 25/03/16
 * Time: 9:04 PM
 */

namespace AppBundle\Document\Weibo;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Groups;

/**
 * @MongoDB\EmbeddedDocument
 * @ExclusionPolicy("none")
 */
class WeiboMeta {
    /**
     * @MongoDB\Int
     * @Groups({"display"})
     */
    protected $likeCount;
    /**
     * @MongoDB\Int
     * @Groups({"display"})
     */
    protected $commentsCount;
    /**
     * @MongoDB\Int
     * @Groups({"display"})
     */
    protected $repostsCount;

    /**
     * Set likeCount
     *
     * @param int $likeCount
     * @return self
     */
    public function setLikeCount($likeCount)
    {
        $this->likeCount = $likeCount;
        return $this;
    }

    /**
     * Get likeCount
     *
     * @return int $likeCount
     */
    public function getLikeCount()
    {
        return $this->likeCount;
    }

    /**
     * Set commentsCount
     *
     * @param int $commentsCount
     * @return self
     */
    public function setCommentsCount($commentsCount)
    {
        $this->commentsCount = $commentsCount;
        return $this;
    }

    /**
     * Get commentsCount
     *
     * @return int $commentsCount
     */
    public function getCommentsCount()
    {
        return $this->commentsCount;
    }

    /**
     * Set repostsCount
     *
     * @param int $repostsCount
     * @return self
     */
    public function setRepostsCount($repostsCount)
    {
        $this->repostsCount = $repostsCount;
        return $this;
    }

    /**
     * Get repostsCount
     *
     * @return int $repostsCount
     */
    public function getRepostsCount()
    {
        return $this->repostsCount;
    }
}

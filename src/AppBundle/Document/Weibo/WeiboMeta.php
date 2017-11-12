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
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;

/**
 * @MongoDB\EmbeddedDocument
 * @ExclusionPolicy("none")
 */
class WeiboMeta {
    /**
     * @MongoDB\Field(type="int")
     * @Groups({"display"})
     */
    protected $likeCount;
    /**
     * @MongoDB\Field(type="int")
     * @Groups({"display"})
     */
    protected $commentsCount;
    /**
     * @MongoDB\Field(type="int")
     * @Groups({"display"})
     */
    protected $repostsCount;

    /**
     * @VirtualProperty
     * @SerializedName("fb_total_likes")
     * @Groups({"display"})
     * @return int
     */
    public function getFbTotalLikes(){
        return $this->getLikeCount();
    }

    /**
     * @VirtualProperty
     * @SerializedName("fb_total_comments")
     * @Groups({"display"})
     * @return int
     */
    public function getFbTotalComments(){
        return $this->getCommentsCount();
    }

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

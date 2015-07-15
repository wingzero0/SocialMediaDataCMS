<?php
/**
 * User: kit
 * Date: 15/07/15
 * Time: 20:33
 */

namespace AppBundle\Document\Facebook;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;

/**
 * @MongoDB\Document(collection="FacebookFeed")
 * @ExclusionPolicy("none")
 */
class FacebookFeed {
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\Field(type="string", name="fbID")
     */
    protected $fbId;
    /**
     * @MongoDB\String
     */
    protected $message;
    /**
     * @MongoDB\Raw
     */
    protected $likes;
    /**
     * @MongoDB\Raw
     */
    protected $comments;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fbId
     *
     * @param string $fbId
     * @return self
     */
    public function setFbId($fbId)
    {
        $this->fbId = $fbId;
        return $this;
    }

    /**
     * Get fbId
     *
     * @return string $fbId
     */
    public function getFbId()
    {
        return $this->fbId;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     *
     * @return string $message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set commentTotalCount
     *
     * @param int $commentTotalCount
     * @return self
     */
    public function setCommentTotalCount($commentTotalCount)
    {
        $this->commentTotalCount = $commentTotalCount;
        return $this;
    }

    /**
     * Get commentTotalCount
     *
     * @return int $commentTotalCount
     */
    public function getCommentTotalCount()
    {
        return $this->commentTotalCount;
    }

    /**
     * Set likes
     *
     * @param raw $likes
     * @return self
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
        return $this;
    }

    /**
     * Get likes
     *
     * @return raw $likes
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set comments
     *
     * @param raw $comments
     * @return self
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * Get comments
     *
     * @return raw $comments
     */
    public function getComments()
    {
        return $this->comments;
    }
}

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
 * @MongoDB\Document(collection="FacebookFeed", repositoryClass="AppBundle\Repository\Facebook\FacebookFeedRepository")
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
     * @MongoDB\Field(type="string", name="created_time")
     */
    protected $createdTime;
    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\Facebook\FacebookPage")
     */
    protected $fbPage;

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

    public function getShortLink(){
        return "https://www.facebook.com/" . $this->getFbId();
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
     * @return int
     */
    public function getLikesTotalCount()
    {
        $likesRaw = $this->getLikes();
        if (isset($likesRaw["summary"]) && isset($likesRaw["summary"]["total_count"])){
            return intval($likesRaw["summary"]["total_count"]);
        }
        return 0;
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

    /**
     * @return int
     */
    public function getCommentsTotalCount()
    {
        $commentsRaw = $this->getComments();
        if (isset($commentsRaw["summary"]) && isset($commentsRaw["summary"]["total_count"])){
            return intval($commentsRaw["summary"]["total_count"]);
        }
        return 0;
    }

    /**
     * Set fbPage
     *
     * @param AppBundle\Document\Facebook\FacebookPage $fbPage
     * @return self
     */
    public function setFbPage(\AppBundle\Document\Facebook\FacebookPage $fbPage)
    {
        $this->fbPage = $fbPage;
        return $this;
    }

    /**
     * Get fbPage
     *
     * @return AppBundle\Document\Facebook\FacebookPage $fbPage
     */
    public function getFbPage()
    {
        return $this->fbPage;
    }

    /**
     * Set createdTime
     *
     * @param string $createdTime
     * @return self
     */
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;
        return $this;
    }

    /**
     * Get createdTime
     *
     * @return string $createdTime
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }
}

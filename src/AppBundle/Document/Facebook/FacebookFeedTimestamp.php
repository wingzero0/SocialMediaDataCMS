<?php
/**
 * User: kit
 * Date: 7/22/2015
 * Time: 2:38 PM
 */

namespace AppBundle\Document\Facebook;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;

/**
 * @MongoDB\Document(collection="FacebookFeedTimestamp", repositoryClass="AppBundle\Repository\Facebook\FacebookFeedTimestampRepository")
 * @ExclusionPolicy("none")
 */
class FacebookFeedTimestamp {
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\Field(type="int", name="likes_total_count")
     */
    protected $likesTotalCount;
    /**
     * @MongoDB\Field(type="int", name="comments_total_count")
     */
    protected $commentsTotalCount;
    /**
     * @MongoDB\Date
     */
    protected $updateTime;
    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\Facebook\FacebookPage")
     */
    protected $fbPage;
    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\Facebook\FacebookFeed")
     */
    protected $fbFeed;


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
     * Set likesTotalCount
     *
     * @param int $likesTotalCount
     * @return self
     */
    public function setLikesTotalCount($likesTotalCount)
    {
        $this->likesTotalCount = $likesTotalCount;
        return $this;
    }

    /**
     * Get likesTotalCount
     *
     * @return int $likesTotalCount
     */
    public function getLikesTotalCount()
    {
        return $this->likesTotalCount;
    }

    /**
     * Set commentsTotalCount
     *
     * @param int $commentsTotalCount
     * @return self
     */
    public function setCommentsTotalCount($commentsTotalCount)
    {
        $this->commentsTotalCount = $commentsTotalCount;
        return $this;
    }

    /**
     * Get commentsTotalCount
     *
     * @return int $commentsTotalCount
     */
    public function getCommentsTotalCount()
    {
        return $this->commentsTotalCount;
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
     * Set fbFeed
     *
     * @param AppBundle\Document\Facebook\FacebookFeed $fbFeed
     * @return self
     */
    public function setFbFeed(\AppBundle\Document\Facebook\FacebookFeed $fbFeed)
    {
        $this->fbFeed = $fbFeed;
        return $this;
    }

    /**
     * Get fbFeed
     *
     * @return AppBundle\Document\Facebook\FacebookFeed $fbFeed
     */
    public function getFbFeed()
    {
        return $this->fbFeed;
    }

    /**
     * Set updateTime
     *
     * @param date $updateTime
     * @return self
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
        return $this;
    }

    /**
     * Get updateTime
     *
     * @return date $updateTime
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }
}

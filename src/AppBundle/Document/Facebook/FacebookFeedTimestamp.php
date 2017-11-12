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
class FacebookFeedTimestamp
{
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\Field(type="int", name="reactions_angry_total_count")
     */
    protected $angryTotalCount;
    /**
     * @MongoDB\Field(type="int", name="reactions_haha_total_count")
     */
    protected $hahaTotalCount;
    /**
     * @MongoDB\Field(type="int", name="reactions_like_total_count")
     */
    protected $likeTotalCount;
    /**
     * @MongoDB\Field(type="int", name="reactions_love_total_count")
     */
    protected $loveTotalCount;
    /**
     * @MongoDB\Field(type="int", name="reactions_sad_total_count")
     */
    protected $sadTotalCount;
    /**
     * @MongoDB\Field(type="int", name="reactions_wow_total_count")
     */
    protected $wowTotalCount;
    /**
     * @MongoDB\Field(type="int", name="shares_total_count")
     */
    protected $sharesTotalCount;
    /**
     * @MongoDB\Field(type="int", name="comments_total_count")
     */
    protected $commentsTotalCount;
    /**
     * @MongoDB\Field(type="date")
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
     * Set angryTotalCount
     *
     * @param int $angryTotalCount
     * @return self
     */
    public function setAngryTotalCount($angryTotalCount)
    {
        $this->angryTotalCount = $angryTotalCount;
        return $this;
    }

    /**
     * Get angryTotalCount
     *
     * @return int $angryTotalCount
     */
    public function getAngryTotalCount()
    {
        return $this->angryTotalCount;
    }

    /**
     * Set hahaTotalCount
     *
     * @param int $hahaTotalCount
     * @return self
     */
    public function setHahaTotalCount($hahaTotalCount)
    {
        $this->hahaTotalCount = $hahaTotalCount;
        return $this;
    }

    /**
     * Get hahaTotalCount
     *
     * @return int $hahaTotalCount
     */
    public function getHahaTotalCount()
    {
        return $this->hahaTotalCount;
    }

    /**
     * Set likeTotalCount
     *
     * @param int $likeTotalCount
     * @return self
     */
    public function setLikeTotalCount($likeTotalCount)
    {
        $this->likeTotalCount = $likeTotalCount;
        return $this;
    }

    /**
     * Get likeTotalCount
     *
     * @return int $likeTotalCount
     */
    public function getLikeTotalCount()
    {
        return $this->likeTotalCount;
    }

    /**
     * Set loveTotalCount
     *
     * @param int $loveTotalCount
     * @return self
     */
    public function setLoveTotalCount($loveTotalCount)
    {
        $this->loveTotalCount = $loveTotalCount;
        return $this;
    }

    /**
     * Get loveTotalCount
     *
     * @return int $loveTotalCount
     */
    public function getLoveTotalCount()
    {
        return $this->loveTotalCount;
    }

    /**
     * Set sadTotalCount
     *
     * @param int $sadTotalCount
     * @return self
     */
    public function setSadTotalCount($sadTotalCount)
    {
        $this->sadTotalCount = $sadTotalCount;
        return $this;
    }

    /**
     * Get sadTotalCount
     *
     * @return int $sadTotalCount
     */
    public function getWowTotalCount()
    {
        return $this->wowTotalCount;
    }

    /**
     * Set wowTotalCount
     *
     * @param int $wowTotalCount
     * @return self
     */
    public function setWowTotalCount($wowTotalCount)
    {
        $this->sadTotalCount = $sadTotalCount;
        return $this;
    }

    /**
     * Get sadTotalCount
     *
     * @return int $sadTotalCount
     */
    public function getSadTotalCount()
    {
        return $this->sadTotalCount;
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
     * Set sharesTotalCount
     *
     * @param int $sharesTotalCount
     * @return self
     */
    public function setSharesTotalCount($sharesTotalCount)
    {
        $this->sharesTotalCount = $sharesTotalCount;
        return $this;
    }

    /**
     * Get sharesTotalCount
     *
     * @return int $sharesTotalCount
     */
    public function getSharesTotalCount()
    {
        return $this->sharesTotalCount;
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

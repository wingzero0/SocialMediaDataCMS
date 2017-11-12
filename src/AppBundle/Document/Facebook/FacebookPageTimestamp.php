<?php

namespace AppBundle\Document\Facebook;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use AppBundle\Document\Facebook\FacebookPage;

/**
 * @MongoDB\Document(collection="FacebookPageTimestamp", repositoryClass="AppBundle\Repository\Facebook\FacebookPageTimestampRepository")
 * @ExclusionPolicy("none")
 */
class FacebookPageTimestamp
{
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\Field(type="int", name="fan_count")
     */
    protected $fanCount;
    /**
     * @MongoDB\Field(type="int", name="talking_about_count")
     */
    protected $talkingAboutCount;
    /**
     * @MongoDB\Field(type="int", name="were_here_count")
     */
    protected $wereHereCount;
    /**
     * @MongoDB\Field(type="float", name="overall_star_rating")
     */
    protected $overallStarRating;
    /**
     * @MongoDB\Field(type="int", name="rating_count")
     */
    protected $ratingCount;
    /**
     * @MongoDB\Field(type="date")
     */
    protected $updateTime;
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
     * Set fanCount
     *
     * @param int $fanCount
     * @return self
     */
    public function setFanCount($fanCount)
    {
        $this->fanCount = $fanCount;
        return $this;
    }

    /**
     * Get fanCount
     *
     * @return int $fanCount
     */
    public function getFanCount()
    {
        return $this->fanCount;
    }

    /**
     * Set talkingAboutCount
     *
     * @param int $talkingAboutCount
     * @return self
     */
    public function setTalkingAboutCount($talkingAboutCount)
    {
        $this->talkingAboutCount = $talkingAboutCount;
        return $this;
    }

    /**
     * Get talkingAboutCount
     *
     * @return int $talkingAboutCount
     */
    public function getTalkingAboutCount()
    {
        return $this->talkingAboutCount;
    }

    /**
     * Set wereHereCount
     *
     * @param int $wereHereCount
     * @return self
     */
    public function setWereHereCount($wereHereCount)
    {
        $this->wereHereCount = $wereHereCount;
        return $this;
    }

    /**
     * Get wereHereCount
     *
     * @return int $wereHereCount
     */
    public function getWereHereCount()
    {
        return $this->wereHereCount;
    }

    /**
     * Get overallStarRating
     *
     * @return float $overallStarRating
     */
    public function getOverallStarRating()
    {
        return $this->overallStarRating;
    }

    /**
     * Get ratingCount
     *
     * @return int $ratingCount
     */
    public function getRatingCount()
    {
        return $this->ratingCount;
    }

    /**
     * Set fbPage
     *
     * @param FacebookPage $fbPage
     * @return self
     */
    public function setFbPage(FacebookPage $fbPage)
    {
        $this->fbPage = $fbPage;
        return $this;
    }

    /**
     * Get fbPage
     *
     * @return FacebookPage $fbPage
     */
    public function getFbPage()
    {
        return $this->fbPage;
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

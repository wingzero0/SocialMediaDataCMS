<?php
/**
 * User: kit
 * Date: 01/07/15
 * Time: 20:12
 */

namespace AppBundle\Document\Facebook;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;

/**
 * @MongoDB\Document(collection="FacebookPage", repositoryClass="AppBundle\Repository\Facebook\FacebookPageRepository")
 * @ExclusionPolicy("none")
 */
class FacebookPage {
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
    protected $name;
    /**
     * @MongoDB\Int
     */
    protected $likes;
    /**
     * @MongoDB\Int
     */
    protected $checkins;
    /**
     * @MongoDB\Field(type="int", name="talking_about_count")
     */
    protected $talkingAboutCount;
    /**
     * @MongoDB\Field(type="int", name="were_here_count")
     */
    protected $wereHereCount;
    /**
     * @MongoDB\Boolean
     */
    protected $exception;


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
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set likes
     *
     * @param int $likes
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
     * @return int $likes
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set checkins
     *
     * @param int $checkins
     * @return self
     */
    public function setCheckins($checkins)
    {
        $this->checkins = $checkins;
        return $this;
    }

    /**
     * Get checkins
     *
     * @return int $checkins
     */
    public function getCheckins()
    {
        return $this->checkins;
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
     * Set excpetion
     *
     * @param boolean $exception
     * @return self
     */
    public function setException($exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * Get excpetion
     *
     * @return boolean $excpetion
     */
    public function getException()
    {
        return $this->exception;
    }
}

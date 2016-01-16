<?php
/**
 * User: kit
 * Date: 15/07/15
 * Time: 23:50
 */

namespace AppBundle\Document\Facebook;

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
class FacebookMeta {
    /**
     * @MongoDB\String
     */
    protected $fbId;
    /**
     * @MongoDB\Int
     * @Groups({"display"})
     */
    protected $fbTotalLikes;
    /**
     * @MongoDB\Int
     * @Groups({"display"})
     */
    protected $fbTotalComments;

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
     * Set fbTotalLikes
     *
     * @param int $fbTotalLikes
     * @return self
     */
    public function setFbTotalLikes($fbTotalLikes)
    {
        $this->fbTotalLikes = $fbTotalLikes;
        return $this;
    }

    /**
     * Get fbTotalLikes
     *
     * @return int $fbTotalLikes
     */
    public function getFbTotalLikes()
    {
        return $this->fbTotalLikes;
    }

    /**
     * Set fbTotalComments
     *
     * @param int $fbTotalComments
     * @return self
     */
    public function setFbTotalComments($fbTotalComments)
    {
        $this->fbTotalComments = $fbTotalComments;
        return $this;
    }

    /**
     * Get fbTotalComments
     *
     * @return int $fbTotalComments
     */
    public function getFbTotalComments()
    {
        return $this->fbTotalComments;
    }
}

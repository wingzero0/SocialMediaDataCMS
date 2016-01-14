<?php
/**
 * User: kit
 * Date: 14/01/16
 * Time: 8:13 PM
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Accessor;

/**
 * @MongoDB\Document(repositoryClass="AppBundle\Repository\SpotlightAdsRepository")
 * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ExclusionPolicy("none")
 */
class SpotlightAds {
    /**
     * @MongoDB\Id
     * @Groups({"display"})
     */
    protected $id;
    /**
     * @MongoDB\String
     * @MongoDB\Index
     * @Groups({"display"})
     */
    protected $description;
    /**
     * @MongoDB\String
     * @MongoDB\Index
     * @Groups({"display"})
     */
    protected $displaySeq;
    /**
     * @MongoDB\String
     * @Groups({"display"})
     */
    protected $imageLink;
    /**
     * @MongoDB\String
     * @Groups({"display"})
     */
    protected $landingPage;

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
     * Set imageLink
     *
     * @param string $imageLink
     * @return self
     */
    public function setImageLink($imageLink)
    {
        $this->imageLink = $imageLink;
        return $this;
    }

    /**
     * Get imageLink
     *
     * @return string $imageLink
     */
    public function getImageLink()
    {
        return $this->imageLink;
    }

    /**
     * Set landingPage
     *
     * @param string $landingPage
     * @return self
     */
    public function setLandingPage($landingPage)
    {
        $this->landingPage = $landingPage;
        return $this;
    }

    /**
     * Get landingPage
     *
     * @return string $landingPage
     */
    public function getLandingPage()
    {
        return $this->landingPage;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set displaySeq
     *
     * @param string $displaySeq
     * @return self
     */
    public function setDisplaySeq($displaySeq)
    {
        $this->displaySeq = $displaySeq;
        return $this;
    }

    /**
     * Get displaySeq
     *
     * @return string $displaySeq
     */
    public function getDisplaySeq()
    {
        return $this->displaySeq;
    }
}

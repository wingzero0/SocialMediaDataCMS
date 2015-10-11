<?php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use AppBundle\Document\Location;
use AppBundle\Document\User;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;

/**
 * @MongoDB\Document(collection="MnemonoBiz")
 * @ExclusionPolicy("none")
 */
class MnemonoBiz{
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\String
     */
    protected $name;
    /**
     * @MongoDB\String
     */
    protected $shortDesc;
    /**
     * @MongoDB\String
     */
    protected $longDesc;
    /**
     * @MongoDB\String
     */
    protected $category;
    /**
     * @MongoDB\Collection
     */
    protected $tags;
    /**
     * @MongoDB\EmbedOne(targetDocument="Location")
     */
    protected $location;
    /**
     * @MongoDB\Collection
     */
    protected $phones;
    /**
     * @MongoDB\Collection
     */
    protected $faxes;
    /**
     * @MongoDB\Collection
     */
    protected $websites;
    /**
     * @MongoDB\Float
     */
    protected $weighting;
    /**
     * @MongoDB\String
     */
    protected $importFrom;
    /**
     * @MongoDB\ReferenceOne(
     *   discriminatorField="importFrom",
     *   discriminatorMap={
     *     "facebookPage"="Document\Facebook\FacebookPage",
     *     "directory"="Document\Directory"
     *   },
     *   defaultDiscriminatorValue="facebookPage"
     * )
     */
    protected $importFromRef;

    /**
     * @MongoDB\Date
     */
    protected $lastModDate;
    /**
     * @MongoDB\ReferenceOne(targetDocument="User")
     */
    protected $lastModUser;

    /**
     * @MongoDB\Float
     */
    protected $globalScore;

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
     * Set shortDesc
     *
     * @param string $shortDesc
     * @return self
     */
    public function setShortDesc($shortDesc)
    {
        $this->shortDesc = $shortDesc;
        return $this;
    }

    /**
     * Get shortDesc
     *
     * @return string $shortDesc
     */
    public function getShortDesc()
    {
        return $this->shortDesc;
    }

    /**
     * Set longDesc
     *
     * @param string $longDesc
     * @return self
     */
    public function setLongDesc($longDesc)
    {
        $this->longDesc = $longDesc;
        return $this;
    }

    /**
     * Get longDesc
     *
     * @return string $longDesc
     */
    public function getLongDesc()
    {
        return $this->longDesc;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return string $category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set tag
     *
     * @param collection $tags
     * @return self
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get tag
     *
     * @return collection $tag
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set phones
     *
     * @param collection $phones
     * @return self
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;
        return $this;
    }

    /**
     * Get phones
     *
     * @return collection $phones
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set faxes
     *
     * @param collection $faxes
     * @return self
     */
    public function setFaxes($faxes)
    {
        $this->faxes = $faxes;
        return $this;
    }

    /**
     * Get faxes
     *
     * @return collection $faxes
     */
    public function getFaxes()
    {
        return $this->faxes;
    }

    /**
     * Set weighting
     *
     * @param float $weighting
     * @return self
     */
    public function setWeighting($weighting)
    {
        $this->weighting = $weighting;
        return $this;
    }

    /**
     * Get weighting
     *
     * @return float $weighting
     */
    public function getWeighting()
    {
        return $this->weighting;
    }

    /**
     * Set importFrom
     *
     * @param string $importFrom
     * @return self
     */
    public function setImportFrom($importFrom)
    {
        $this->importFrom = $importFrom;
        return $this;
    }

    /**
     * Get importFrom
     *
     * @return string $importFrom
     */
    public function getImportFrom()
    {
        return $this->importFrom;
    }

    /**
     * Set importFromRef
     *
     * @param $importFromRef
     * @return self
     */
    public function setImportFromRef($importFromRef)
    {
        $this->importFromRef = $importFromRef;
        return $this;
    }

    /**
     * Get importFromRef
     *
     * @return $importFromRef
     */
    public function getImportFromRef()
    {
        return $this->importFromRef;
    }

    /**
     * Set websites
     *
     * @param collection $websites
     * @return self
     */
    public function setWebsites($websites)
    {
        $this->websites = $websites;
        return $this;
    }

    /**
     * Get websites
     *
     * @return collection $websites
     */
    public function getWebsites()
    {
        return $this->websites;
    }

    /**
     * Set lastModDate
     *
     * @param date $lastModDate
     * @return self
     */
    public function setLastModDate($lastModDate)
    {
        $this->lastModDate = $lastModDate;
        return $this;
    }

    /**
     * Get lastModDate
     *
     * @return date $lastModDate
     */
    public function getLastModDate()
    {
        return $this->lastModDate;
    }

    /**
     * Set location
     *
     * @param AppBundle\Document\Location $location
     * @return self
     */
    public function setLocation(\AppBundle\Document\Location $location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Get location
     *
     * @return AppBundle\Document\Location $location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set lastModUser
     *
     * @param AppBundle\Document\User $lastModUser
     * @return self
     */
    public function setLastModUser(\AppBundle\Document\User $lastModUser)
    {
        $this->lastModUser = $lastModUser;
        return $this;
    }

    /**
     * Get lastModUser
     *
     * @return AppBundle\Document\User $lastModUser
     */
    public function getLastModUser()
    {
        return $this->lastModUser;
    }

    /**
     * Set globalScore
     *
     * @param float $globalScore
     * @return self
     */
    public function setGlobalScore($globalScore)
    {
        $this->globalScore = $globalScore;
        return $this;
    }

    /**
     * Get globalScore
     *
     * @return float $globalScore
     */
    public function getGlobalScore()
    {
        if ($this->globalScore === null){
            return 0.0;
        }
        return $this->globalScore;
    }
}

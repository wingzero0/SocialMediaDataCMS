<?php
namespace AppBundle\Document;

use AppBundle\Document\Facebook\FacebookPage;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use AppBundle\Document\User;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Accessor;

/**
 * @MongoDB\Document(collection="MnemonoBiz", repositoryClass="AppBundle\Repository\MnemonoBizRepository"))
 * @ExclusionPolicy("none")
 * @MongoDB\Indexes(
 *   @MongoDB\Index(keys={"importFrom"="asc", "importFromRef.$id"="desc"}),
 * )
 */
class MnemonoBiz{
    /**
     * @MongoDB\Id
     * @Groups({"display"})
     */
    protected $id;
    /**
     * @MongoDB\String
     * @Groups({"display"})
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
    // TODO migrate category to tag?
    /**
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $category;
    /**
     * @MongoDB\Collection
     * @MongoDB\Index
     */
    protected $tags;
    /**
     * @MongoDB\Collection
     */
    protected $addresses;
    /**
     * @MongoDB\Collection
     */
    protected $cities;
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
     * @MongoDB\Date
     * @MongoDB\Index
     */
    protected $lastPostUpdateAt;

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
     * @VirtualProperty
     * @SerializedName("profile_pic_link")
     * @Groups({"display"})
     * @return string|null
     */
    public function getProfilePicLink(){
        $discriminator = $this->getImportFrom();
        if ($discriminator == "facebookPage"){
            $fbPage = $this->getImportFromRef();
            if ($fbPage instanceof FacebookPage){
                return "http://graph.facebook.com/" . $fbPage->getFbId() . "/picture?type=large";
            }
        }
        return null;
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
        if ($this->weighting === null){
            $this->setWeighting(1.0);
        }
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
     * Set lastModUser
     *
     * @param User $lastModUser
     * @return self
     */
    public function setLastModUser(User $lastModUser)
    {
        $this->lastModUser = $lastModUser;
        return $this;
    }

    /**
     * Get lastModUser
     *
     * @return User $lastModUser
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

    /**
     * Set lastPostUpdateAt
     *
     * @param \DateTime $lastPostUpdateAt
     * @return self
     */
    public function setLastPostUpdateAt(\DateTime $lastPostUpdateAt)
    {
        if ($this->lastPostUpdateAt == null || $lastPostUpdateAt > $this->lastPostUpdateAt){
            $this->lastPostUpdateAt = $lastPostUpdateAt;
        }
        return $this;
    }

    /**
     * Get lastPostUpdateAt
     *
     * @return date $lastPostUpdateAt
     */
    public function getLastPostUpdateAt()
    {
        return $this->lastPostUpdateAt;
    }

    /**
     * Set cities
     *
     * @param collection $cities
     * @return self
     */
    public function setCities($cities)
    {
        $this->cities = $cities;
        return $this;
    }

    /**
     * Get cities
     *
     * @return collection $cities
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Set addresses
     *
     * @param collection $addresses
     * @return self
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
        return $this;
    }

    /**
     * Get addresses
     *
     * @return collection $addresses
     */
    public function getAddresses()
    {
        return $this->addresses;
    }
}

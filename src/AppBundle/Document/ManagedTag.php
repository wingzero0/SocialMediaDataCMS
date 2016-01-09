<?php
/**
 * User: kit
 * Date: 8/1/2016
 * Time: 13:42
 */

namespace AppBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use AppBundle\Document\Location;
use AppBundle\Document\User;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Groups;

/**
 * @MongoDB\Document(collection="ManagedTag", repositoryClass="AppBundle\Repository\ManagedTagRepository"))
 * @ExclusionPolicy("none")
 */
class ManagedTag {
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\String
     * @MongoDB\Index
     * @Groups({"display"})
     */
    protected $key;
    /**
     * @MongoDB\String
     * @MongoDB\Index
     * @Groups({"display"})
     */
    protected $nameChi;
    /**
     * @MongoDB\String
     * @MongoDB\Index
     * @Groups({"display"})
     */
    protected $nameEng;
    /**
     * @MongoDB\Collection
     * @MongoDB\Index
     */
    protected $extendTags;

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
     * Set key
     *
     * @param string $key
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get key
     *
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set nameChi
     *
     * @param string $nameChi
     * @return self
     */
    public function setNameChi($nameChi)
    {
        $this->nameChi = $nameChi;
        return $this;
    }

    /**
     * Get nameChi
     *
     * @return string $nameChi
     */
    public function getNameChi()
    {
        return $this->nameChi;
    }

    /**
     * Set nameEng
     *
     * @param string $nameEng
     * @return self
     */
    public function setNameEng($nameEng)
    {
        $this->nameEng = $nameEng;
        return $this;
    }

    /**
     * Get nameEng
     *
     * @return string $nameEng
     */
    public function getNameEng()
    {
        return $this->nameEng;
    }
}

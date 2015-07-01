<?php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
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
     * @MongoDB\string
     */
    protected $name;

    /**
     * @MongoDB\collection
     */
    protected $urls;

    /**
     * @MongoDB\string
     * @Since("1.1")
     */
    protected $address;



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
     * Set urls
     *
     * @param collection $urls
     * @return self
     */
    public function setUrls($urls)
    {
        $this->urls = $urls;
        return $this;
    }

    /**
     * Get urls
     *
     * @return collection $urls
     */
    public function getUrls()
    {
        return $this->urls;
    }


    /**
     * Set address
     *
     * @param string $address
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return string $address
     */
    public function getAddress()
    {
        return $this->address;
    }
}

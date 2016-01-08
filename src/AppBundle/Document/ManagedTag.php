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
     */
    protected $name;

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
}

<?php
/**
 * User: kit
 * Date: 17/10/15
 * Time: 3:31 PM
 */

namespace AppBundle\Document\Settings;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;

/**
 * @MongoDB\Document(collection="Weighting", repositoryClass="AppBundle\Repository\Settings\WeightingRepository")
 * @ExclusionPolicy("none")
 */
class Weighting {
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
     * @MongoDB\Float
     */
    protected $value;

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
     * Set value
     *
     * @param float $value
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return float $value
     */
    public function getValue()
    {
        return $this->value;
    }
}

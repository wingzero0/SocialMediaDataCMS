<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="TrendingPost", repositoryClass="AppBundle\Repository\TrendingPostRepository")
 * @MongoDB\Indexes(
 *   @MongoDB\Index(keys={"key"="desc"}),
 * )
 */
class TrendingPost
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $key;

    /**
     * @MongoDB\Field(type="string")
     */
    private $metric;

    /**
     * @MongoDB\Field(type="date")
     */
    private $from;

    /**
     * @MongoDB\Field(type="date")
     */
    private $to;

    /**
     * @MongoDB\Field(type="collection")
     */
    private $value = array();

    public function getId()
    {
        return $this->id;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getMetric()
    {
        return $this->metric;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getValue()
    {
        return $this->value;
    }
}

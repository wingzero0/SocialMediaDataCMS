<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(
 *   collection="BizPostMetricStats",
 *   repositoryClass="AppBundle\Repository\BizPostMetricStatsRepo"
 * )
 */
class BizPostMetricStats
{
    /**
     * @MongoDB\Id(strategy="NONE", type="raw")
     */
    private $id;

    /**
     * @MongoDB\Field(type="raw", name="value")
     */
    private $value;

    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }
}

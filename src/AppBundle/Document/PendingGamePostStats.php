<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(
 *   collection="PendingGamePostStats",
 *   repositoryClass="AppBundle\Repository\PendingGamePostStatsRepo"
 * )
 */
class PendingGamePostStats
{
    /**
     * @MongoDB\Id(strategy="NONE", type="string")
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

    /**
     * @return int
     */
    public function getCount()
    {
        $raw = $this->getValue();
        return $raw["count"];
    }

    /**
     * @return int
     */
    public function getKCount()
    {
        $raw = $this->getValue();
        return $raw["k_count"];
    }

    /**
     * @return int
     */
    public function getNbCount()
    {
        $raw = $this->getValue();
        return $raw["nb_count"];
    }

    /**
     * @return int
     */
    public function getKnbCount()
    {
        $raw = $this->getValue();
        return $raw["knb_count"];
    }

    /**
     * @return int
     */
    public function getGameCount()
    {
        $raw = $this->getValue();
        return $raw["game_count"];
    }


}


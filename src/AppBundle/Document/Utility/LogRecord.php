<?php
/**
 * User: kit
 * Date: 17/01/16
 * Time: 5:40 PM
 */

namespace AppBundle\Document\Utility;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;
use \DateTime;

/**
 * @MongoDB\Document(collection="LogRecord", repositoryClass="AppBundle\Repository\Utility\LogRecordRepository")
 * @ExclusionPolicy("none")
 */
class LogRecord {
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\Field(type="date")
     * @MongoDB\Index
     */
    protected $logTime;
    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\Index
     */
    protected $category;
    /**
     * @MongoDB\Field(type="string")
     */
    protected $remark;

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
     * Set logTime
     *
     * @param DateTime $logTime
     * @return self
     */
    public function setLogTime($logTime)
    {
        $this->logTime = $logTime;
        return $this;
    }

    /**
     * Get logTime
     *
     * @return DateTime $logTime
     */
    public function getLogTime()
    {
        return $this->logTime;
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
     * Set remark
     *
     * @param string $remark
     * @return self
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
        return $this;
    }

    /**
     * Get remark
     *
     * @return string $remark
     */
    public function getRemark()
    {
        return $this->remark;
    }
}

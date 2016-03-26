<?php
/**
 * User: kit
 * Date: 14/03/16
 * Time: 6:54 PM
 */

namespace AppBundle\Document\Weibo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;


/**
 * @MongoDB\Document(collection="WeiboPage", repositoryClass="AppBundle\Repository\Weibo\WeiboPageRepository")
 * @ExclusionPolicy("none")
 */
class WeiboPage {
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $uid;
    /**
     * @MongoDB\String
     */
    protected $description;
    /**
     * @MongoDB\String
     */
    protected $name;
    /**
     * @MongoDB\Raw
     */
    protected $mnemono;
    /**
     * @MongoDB\Boolean
     */
    protected $exception;

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
     * Set uid
     *
     * @param string $uid
     * @return self
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * Get uid
     *
     * @return string $uid
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
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
     * Set mnemono
     *
     * @param raw $mnemono
     * @return self
     */
    public function setMnemono($mnemono)
    {
        $this->mnemono = $mnemono;
        return $this;
    }

    /**
     * Get mnemono
     *
     * @return raw $mnemono
     */
    public function getMnemono()
    {
        return $this->mnemono;
    }

    /**
     * @return string|null
     */
    public function getCity(){
        $mnemonoArray = $this->getMnemono();
        if (isset($mnemonoArray["location"]) && $mnemonoArray["location"]["city"]){
            return $mnemonoArray["location"]["city"];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getCategory(){
        $mnemonoArray = $this->getMnemono();
        if (isset($mnemonoArray["category"])){
            return $mnemonoArray["category"];
        }
        return null;
    }

    /**
     * Set excpetion
     *
     * @param boolean $exception
     * @return self
     */
    public function setException($exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * Get excpetion
     *
     * @return boolean $excpetion
     */
    public function getException()
    {
        return $this->exception;
    }
}

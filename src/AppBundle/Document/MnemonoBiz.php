<?php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="MnemonoBiz")
 */
class MnemonoBiz implements \JsonSerializable
{
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
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return array( 'name' => $this->getName(), 'urls' => $this->getUrls() );
    }
}

<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 5/19/2017
 * Time: 10:33 AM
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Groups;

/**
 * @MongoDB\EmbeddedDocument
 * @ExclusionPolicy("none")
 */
class Image
{
    /**
     * @MongoDB\Field(type="string")
     * @Groups({"display"})
     */
    protected $src;
    /**
     * @MongoDB\Field(type="int")
     * @Groups({"display"})
     */
    protected $width;
    /**
     * @MongoDB\Field(type="int")
     * @Groups({"display"})
     */
    protected $height;

    public static function createFromArray($arr){
        $image = new Image();
        $image->setHeight($arr["height"]);
        $image->setSrc($arr["src"]);
        $image->setWidth($arr["width"]);
        return $image;
    }

    /**
     * Set src
     *
     * @param string $src
     * @return $this
     */
    public function setSrc($src)
    {
        $this->src = $src;
        return $this;
    }

    /**
     * Get src
     *
     * @return string $src
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * Set width
     *
     * @param int $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get width
     *
     * @return int $width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param int $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get height
     *
     * @return int $height
     */
    public function getHeight()
    {
        return $this->height;
    }
}

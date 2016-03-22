<?php
/**
 * User: kit
 * Date: 20/03/16
 * Time: 6:20 PM
 */

namespace AppBundle\Document\Weibo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;


/**
 * @MongoDB\Document(collection="WeiboFeed", repositoryClass="AppBundle\Repository\Weibo\WeiboPageRepository")
 * @ExclusionPolicy("none")
 */
class WeiboFeed {
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\String
     */
    protected $mid;
    /**
     * @MongoDB\String
     */
    protected $text;
    /**
     * @MongoDB\Int
     */
    protected $like_count;
    /**
     * @MongoDB\Int
     */
    protected $comments_count;
    /**
     * @MongoDB\Raw
     */
    protected $pics;
}
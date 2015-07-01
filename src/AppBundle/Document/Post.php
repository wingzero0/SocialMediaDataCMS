<?php
/**
 * User: kit
 * Date: 01/07/15
 * Time: 22:34
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\CommentBundle\Document\Thread as BaseThread;

/**
 * @MongoDB\Document(collection="Post")
 * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Post extends BaseThread{
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\String
     */
    protected $message;
}
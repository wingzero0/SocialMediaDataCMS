<?php
/**
 * User: kit
 * Date: 10/28/2015
 * Time: 9:50 AM
 */

namespace AppBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use AppBundle\Document\Post;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;

/**
 * @MongoDB\Document(collection="PostForReview", repositoryClass="AppBundle\Repository\PostForReviewRepository")
 * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ExclusionPolicy("none")
 */
class PostForReview {
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\Post")
     * @MongoDB\Index
     */
    protected $post;
    /**
     * @MongoDB\Int
     * @MongoDB\Index
     */
    protected $batch;

    /**
     * @MongoDB\Int
     * @MongoDB\Index
     */
    protected $rankPosition;

    /**
     * @MongoDB\Collection
     * @MongoDB\Index
     */
    protected $tags;

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
     * Set post
     *
     * @param Post $post
     * @return self
     */
    public function setPost(Post $post)
    {
        $this->post = $post;
        return $this;
    }

    /**
     * Get post
     *
     * @return Post $post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set batch
     *
     * @param int $batch
     * @return self
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;
        return $this;
    }

    /**
     * Get batch
     *
     * @return int $batch
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * Set rankPosition
     *
     * @param int $rankPosition
     * @return self
     */
    public function setRankPosition($rankPosition)
    {
        $this->rankPosition = $rankPosition;
        return $this;
    }

    /**
     * Get rankPosition
     *
     * @return int $rankPosition
     */
    public function getRankPosition()
    {
        return $this->rankPosition;
    }

    /**
     * Set tags
     *
     * @param collection $tags
     * @return self
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get tags
     *
     * @return collection $tags
     */
    public function getTags()
    {
        return $this->tags;
    }
}

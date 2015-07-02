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
    protected $fbId;
    /**
     * @MongoDB\Int
     */
    protected $fbTotalLikes;
    /**
     * @MongoDB\Int
     */
    protected $fbTotalComments;
    /**
     * @MongoDB\Collection
     */
    protected $tags;
    /**
     * @MongoDB\String
     */
    protected $mnemonoCat;
    /**
     * @MongoDB\Date
     */
    protected $fbUpdateTime;
    /**
     * @MongoDB\Float
     */
    protected $rankingScoreAlgorithm;
    /**
     * @MongoDB\Float
     */
    protected $rankingScoreHuman;
    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\MnemonoBiz")
     */
    protected $mnemonoBiz;
    /**
     * @MongoDB\String
     */
    protected $content;

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
     * Set fbId
     *
     * @param string $fbId
     * @return self
     */
    public function setFbId($fbId)
    {
        $this->fbId = $fbId;
        return $this;
    }

    /**
     * Get fbId
     *
     * @return string $fbId
     */
    public function getFbId()
    {
        return $this->fbId;
    }

    /**
     * Set fbTotalLikes
     *
     * @param int $fbTotalLikes
     * @return self
     */
    public function setFbTotalLikes($fbTotalLikes)
    {
        $this->fbTotalLikes = $fbTotalLikes;
        return $this;
    }

    /**
     * Get fbTotalLikes
     *
     * @return int $fbTotalLikes
     */
    public function getFbTotalLikes()
    {
        return $this->fbTotalLikes;
    }

    /**
     * Set fbTotalComments
     *
     * @param int $fbTotalComments
     * @return self
     */
    public function setFbTotalComments($fbTotalComments)
    {
        $this->fbTotalComments = $fbTotalComments;
        return $this;
    }

    /**
     * Get fbTotalComments
     *
     * @return int $fbTotalComments
     */
    public function getFbTotalComments()
    {
        return $this->fbTotalComments;
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

    /**
     * Set mnemonoCat
     *
     * @param string $mnemonoCat
     * @return self
     */
    public function setMnemonoCat($mnemonoCat)
    {
        $this->mnemonoCat = $mnemonoCat;
        return $this;
    }

    /**
     * Get mnemonoCat
     *
     * @return string $mnemonoCat
     */
    public function getMnemonoCat()
    {
        return $this->mnemonoCat;
    }

    /**
     * Set fbUpdateTime
     *
     * @param date $fbUpdateTime
     * @return self
     */
    public function setFbUpdateTime($fbUpdateTime)
    {
        $this->fbUpdateTime = $fbUpdateTime;
        return $this;
    }

    /**
     * Get fbUpdateTime
     *
     * @return date $fbUpdateTime
     */
    public function getFbUpdateTime()
    {
        return $this->fbUpdateTime;
    }

    /**
     * Set rankingScoreAlgorithm
     *
     * @param float $rankingScoreAlgorithm
     * @return self
     */
    public function setRankingScoreAlgorithm($rankingScoreAlgorithm)
    {
        $this->rankingScoreAlgorithm = $rankingScoreAlgorithm;
        return $this;
    }

    /**
     * Get rankingScoreAlgorithm
     *
     * @return float $rankingScoreAlgorithm
     */
    public function getRankingScoreAlgorithm()
    {
        return $this->rankingScoreAlgorithm;
    }

    /**
     * Set rankingScoreHuman
     *
     * @param float $rankingScoreHuman
     * @return self
     */
    public function setRankingScoreHuman($rankingScoreHuman)
    {
        $this->rankingScoreHuman = $rankingScoreHuman;
        return $this;
    }

    /**
     * Get rankingScoreHuman
     *
     * @return float $rankingScoreHuman
     */
    public function getRankingScoreHuman()
    {
        return $this->rankingScoreHuman;
    }

    /**
     * Set mnemonoBiz
     *
     * @param AppBundle\Document\MnemonoBiz $mnemonoBiz
     * @return self
     */
    public function setMnemonoBiz(\AppBundle\Document\MnemonoBiz $mnemonoBiz)
    {
        $this->mnemonoBiz = $mnemonoBiz;
        return $this;
    }

    /**
     * Get mnemonoBiz
     *
     * @return AppBundle\Document\MnemonoBiz $mnemonoBiz
     */
    public function getMnemonoBiz()
    {
        return $this->mnemonoBiz;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return string $content
     */
    public function getContent()
    {
        return $this->content;
    }
}

<?php
/**
 * User: kit
 * Date: 01/07/15
 * Time: 22:34
 */

namespace AppBundle\Document;

use AppBundle\Document\MnemonoBiz;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\CommentBundle\Document\Thread as BaseThread;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;

/**
 * @MongoDB\Document(collection="Post", repositoryClass="AppBundle\Repository\PostRepository")
 * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ExclusionPolicy("none")
 */
class Post extends BaseThread{
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\Collection
     * @MongoDB\Index
     */
    protected $tags;
    /**
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $mnemonoCat;
    /**
     * @MongoDB\Date
     */
    protected $lastModDate;
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
     * @MongoDB\Index
     */
    protected $mnemonoBiz;
    /**
     * @MongoDB\String
     */
    protected $importFrom;
    /**
     * @MongoDB\ReferenceOne(
     *   discriminatorField="importFrom",
     *   discriminatorMap={
     *     "facebookFeed"="Document\Facebook\FacebookFeed",
     *     "directory"="Document\Directory"
     *   },
     *   defaultDiscriminatorValue="facebookFeed"
     * )
     * @MongoDB\Index
     */
    protected $importFromRef;
    /**
     * @MongoDB\EmbedOne(
     *   discriminatorField="importFrom",
     *   discriminatorMap={
     *     "facebookFeed"="Document\Facebook\FacebookMeta"
     *   },
     *   defaultDiscriminatorValue="facebookFeed"
     * )
     */
    protected $meta;
    /**
     * @MongoDB\String
     */
    // passible value: draft(by use), review(by admin), published,
    protected $publishStatus;
    /**
     * @MongoDB\String
     */
    protected $content;
    /**
     * @MongoDB\Float
     */
    protected $adminScore;
    /**
     * @MongoDB\Float
     */
    protected $localScore;
    /**
     * @MongoDB\Float
     */
    protected $finalScore;

    public function updateFinalScore($localWeight = 1.0, $globalWeight = 1.0, $adminWeight = 1.0){
        $global = $this->getMnemonoBiz()->getGlobalScore();
        $local = $this->getLocalScore();
        $admin = $this->getAdminScore();
        $finalScore = $globalWeight * $global + $localWeight * $local + $adminWeight * $admin;
        $this->setFinalScore( $finalScore );
        return $finalScore;
    }

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
     * @return MnemonoBiz $mnemonoBiz
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

    /**
     * Set lastModDate
     *
     * @param date $lastModDate
     * @return self
     */
    public function setLastModDate($lastModDate)
    {
        $this->lastModDate = $lastModDate;
        return $this;
    }

    /**
     * Get lastModDate
     *
     * @return date $lastModDate
     */
    public function getLastModDate()
    {
        return $this->lastModDate;
    }

    /**
     * Set importFrom
     *
     * @param string $importFrom
     * @return self
     */
    public function setImportFrom($importFrom)
    {
        $this->importFrom = $importFrom;
        return $this;
    }

    /**
     * Get importFrom
     *
     * @return string $importFrom
     */
    public function getImportFrom()
    {
        return $this->importFrom;
    }

    /**
     * Set importFromRef
     *
     * @param $importFromRef
     * @return self
     */
    public function setImportFromRef($importFromRef)
    {
        $this->importFromRef = $importFromRef;
        return $this;
    }

    /**
     * Get importFromRef
     *
     * @return $importFromRef
     */
    public function getImportFromRef()
    {
        return $this->importFromRef;
    }

    /**
     * Set meta
     *
     * @param $meta
     * @return self
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     * Get meta
     *
     * @return $meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set publishStatus
     *
     * @param string $publishStatus
     * @return self
     */
    public function setPublishStatus($publishStatus)
    {
        $this->publishStatus = $publishStatus;
        return $this;
    }

    /**
     * Get publishStatus
     *
     * @return string $publishStatus
     */
    public function getPublishStatus()
    {
        return $this->publishStatus;
    }

    /**
     * Set localScore
     *
     * @param float $localScore
     * @return self
     */
    public function setLocalScore($localScore)
    {
        $this->localScore = $localScore;
        return $this;
    }

    /**
     * Get localScore
     *
     * @return float $localScore
     */
    public function getLocalScore()
    {
        return $this->localScore;
    }

    /**
     * Set adminScore
     *
     * @param float $adminScore
     * @return self
     */
    public function setAdminScore($adminScore)
    {
        $this->adminScore = $adminScore;
        return $this;
    }

    /**
     * Get adminScore
     *
     * @return float $adminScore
     */
    public function getAdminScore()
    {
        if ($this->adminScore === null){
            return 0.0;
        }
        return $this->adminScore;
    }

    /**
     * Set finalScore
     *
     * @param float $finalScore
     * @return self
     */
    public function setFinalScore($finalScore)
    {
        $this->finalScore = $finalScore;
        return $this;
    }

    /**
     * Get finalScore
     *
     * @return float $finalScore
     */
    public function getFinalScore()
    {
        return $this->finalScore;
    }
}

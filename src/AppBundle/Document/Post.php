<?php
/**
 * User: kit
 * Date: 01/07/15
 * Time: 22:34
 */

namespace AppBundle\Document;

use AppBundle\Document\MnemonoBiz;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\CommentBundle\Document\Thread as BaseThread;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Accessor;

/**
 * @MongoDB\Document(collection="Post", repositoryClass="AppBundle\Repository\PostRepository")
 * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ExclusionPolicy("none")
 * @MongoDB\Indexes(
 *   @MongoDB\Index(keys={"mnemonoBiz"="desc", "updateAt"="desc"}),
 *   @MongoDB\Index(keys={"importFrom"="asc", "importFromRef.$id"="desc"}),
 *   @MongoDB\Index(keys={"rankPosition"="asc", "finalScore"="desc"}),
 *   @MongoDB\Index(keys={"expireDate"="desc", "rankPosition"="asc", "finalScore"="desc"}),
 * )
 */
class Post extends BaseThread{
    /**
     * @MongoDB\Id
     * @Groups({"display"})
     */
    protected $id;
    /**
     * @MongoDB\Collection
     * @MongoDB\Index
     * @Groups({"display"})
     */
    protected $tags;
    /**
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $mnemonoCat;
    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\MnemonoBiz")
     * @MongoDB\Index
     * @Accessor(getter="getWrappedMnemonoBiz",setter="setMnemonoBiz")
     * @Groups({"display"})
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
     */
    protected $importFromRef;
    /**
     * @MongoDB\String
     * @Groups({"display"})
     */
    protected $originalLink;
    /**
     * @MongoDB\Collection
     * @Groups({"display"})
     */
    protected $imageLinks;
    /**
     * @MongoDB\EmbedOne(
     *   discriminatorField="importFrom",
     *   discriminatorMap={
     *     "facebookFeed"="Document\Facebook\FacebookMeta"
     *   },
     *   defaultDiscriminatorValue="facebookFeed"
     * )
     * @Groups({"display"})
     */
    protected $meta;
    /**
     * @MongoDB\String
     */
    protected $publishStatus;
    // passible value: draft(by use), review(by admin), published,
    /**
     * @MongoDB\String
     * @Groups({"display"})
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
     * @MongoDB\Index
     */
    protected $finalScore;
    /**
     * @MongoDB\Int
     */
    protected $rankPosition;
    /**
     * @MongoDB\Date
     */
    protected $createAt;
    /**
     * @MongoDB\Date
     * @MongoDB\Index
     * @Groups({"display"})
     */
    protected $updateAt;

    /**
     * @deprecated
     * @MongoDB\Boolean
     * @MongoDB\Index
     */
    protected $spotlight;
    /**
     * @MongoDB\Boolean
     * @MongoDB\Index
     */
    protected $showAtHomepage;

    /**
     * @MongoDB\Date
     * @MongoDB\Index
     */
    protected $expireDate;
    /**
     * @MongoDB\Boolean
     * @MongoDB\Index
     */
    protected $softDelete;

    /**
     * @return array key to label
     */
    public static function listOfPublishStatus(){
        return array('draft' => 'Draft', 'review' => 'Review', 'published' => 'Published');
    }

    public function __constract(){
        $this->setTags(new ArrayCollection());
        $this->setImageLinks(new ArrayCollection());
    }

    public function updateFinalScore($localWeight = 1.0, $globalWeight = 1.0, $adminWeight = 1.0){
        $global = 0.0;
        if ($this->getMnemonoBiz()){
            $global = $this->getMnemonoBiz()->getGlobalScore();
        }
        $local = $this->getLocalScore();
        $admin = $this->getAdminScore();
        $finalScore = $globalWeight * $global + $localWeight * $local + $adminWeight * $admin;
        $this->setFinalScore( $finalScore );
        return $finalScore;
    }

    public function getBriefContent(){
        $content = $this->getContent();
        $pattern = "/^(.){0,20}/um";
        $matches = array();
        $result = preg_match($pattern, $content, $matches);
        if ($result > 0){
            return $matches[0];
        }else{
            return "";
        }
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
     * Set mnemonoBiz
     *
     * @param MnemonoBiz $mnemonoBiz
     * @return self
     */
    public function setMnemonoBiz(MnemonoBiz $mnemonoBiz)
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

    public function getWrappedMnemonoBiz(){
        $biz = $this->getMnemonoBiz();
        if (!($biz instanceof MnemonoBiz)){
            $biz = new MnemonoBiz();
            $biz->setName("Mnemono");
        }
        return $biz;
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

    /**
     * Set createAt
     *
     * @param date $createAt
     * @return self
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;
        return $this;
    }

    /**
     * Get createAt
     *
     * @return date $createAt
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set updateAt
     *
     * @param date $updateAt
     * @return self
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;
        return $this;
    }

    /**
     * Get updateAt
     *
     * @return date $updateAt
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * Set spotlight
     *
     * @param boolean $spotlight
     * @return self
     */
    public function setSpotlight($spotlight)
    {
        $this->spotlight = $spotlight;
        return $this;
    }

    /**
     * @deprecated
     * Get spotlight
     *
     * @return boolean $spotlight
     */
    public function getSpotlight()
    {
        return $this->spotlight;
    }

    /**
     * @deprecated
     * Get spotlight
     *
     * @return boolean $spotlight
     */
    public function isSpotlight()
    {
        if ($this->spotlight == true){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Set expireDate
     *
     * @param date $expireDate
     * @return self
     */
    public function setExpireDate($expireDate)
    {
        $this->expireDate = $expireDate;
        return $this;
    }

    /**
     * Get expireDate
     *
     * @return date $expireDate
     */
    public function getExpireDate()
    {
        return $this->expireDate;
    }

    /**
     * Set softDelete
     *
     * @param boolean $softDelete
     * @return self
     */
    public function setSoftDelete($softDelete)
    {
        $this->softDelete = $softDelete;
        return $this;
    }

    /**
     * Get softDelete
     *
     * @return boolean $softDelete
     */
    public function getSoftDelete()
    {
        return $this->softDelete;
    }

    /**
     * Set originalLink
     *
     * @param string $originalLink
     * @return self
     */
    public function setOriginalLink($originalLink)
    {
        $this->originalLink = $originalLink;
        return $this;
    }

    /**
     * Get originalLink
     *
     * @return string $originalLink
     */
    public function getOriginalLink()
    {
        return $this->originalLink;
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
     * Set imageLinks
     *
     * @param collection $imageLinks
     * @return self
     */
    public function setImageLinks($imageLinks)
    {
        $this->imageLinks = $imageLinks;
        return $this;
    }

    /**
     * Get imageLinks
     *
     * @return collection $imageLinks
     */
    public function getImageLinks()
    {
        return $this->imageLinks;
    }

    /**
     * Set showAtHomepage
     *
     * @param boolean $showAtHomepage
     * @return self
     */
    public function setShowAtHomepage($showAtHomepage)
    {
        $this->showAtHomepage = $showAtHomepage;
        return $this;
    }

    /**
     * Get showAtHomepage
     *
     * @return boolean $showAtHomepage
     */
    public function getShowAtHomepage()
    {
        if ($this->showAtHomepage == true){
            return true;
        }else{
            return false;
        }
    }

    public function isShowAtHomepage(){
        return $this->getShowAtHomepage();
    }
}

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
use DateTime;
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
class Post extends BaseThread
{
    const FACEBOOK_FEED = 'facebookFeed';
    const WEIBO_FEED = 'weiboFeed';
    const STATUS_DRAFT = "draft";
    const STATUS_REVIEW = "review";
    const STATUS_PUBLISHED = "published";
    /**
     * @MongoDB\Id
     * @Groups({"display"})
     */
    protected $id;
    /**
     * @MongoDB\Field(type="collection")
     * @MongoDB\Index
     * @Groups({"display"})
     */
    protected $tags;
    /**
     * @deprecated
     * @MongoDB\Field(type="string")
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
     * @MongoDB\Field(type="string")
     * @Groups({"display"})
     */
    protected $importFrom;
    /**
     * @MongoDB\ReferenceOne(
     *   discriminatorField="importFrom",
     *   discriminatorMap={
     *     "facebookFeed"="Document\Facebook\FacebookFeed",
     *     "weiboFeed"="Document\Weibo\WeiboFeed"
     *   },
     *   defaultDiscriminatorValue="facebookFeed"
     * )
     */
    protected $importFromRef;
    /**
     * @MongoDB\Field(type="string")
     * @Groups({"display"})
     */
    protected $originalLink;
    /**
     * @MongoDB\Field(type="collection")
     * @Groups({"display"})
     */
    protected $imageLinks;
    /**
     * @MongoDB\EmbedMany(targetDocument="AppBundle\Document\Image")
     * @Groups({"display"})
     */
    protected $images;
    /**
     * @MongoDB\Field(type="collection")
     * @Groups({"display"})
     */
    protected $videoLinks;
    /**
     * @MongoDB\EmbedOne(
     *   discriminatorField="importFrom",
     *   discriminatorMap={
     *     "facebookFeed"="Document\Facebook\FacebookMeta",
     *     "weiboFeed"="Document\Weibo\WeiboMeta"
     *   },
     *   defaultDiscriminatorValue="facebookFeed"
     * )
     * @Groups({"display"})
     */
    protected $meta;
    /**
     * @MongoDB\Field(type="string")
     */
    protected $publishStatus;
    // passible value: draft(by use), review(by admin), published,
    /**
     * @MongoDB\Field(type="string")
     * @Groups({"display"})
     */
    protected $content;
    /**
     * @MongoDB\Field(type="float")
     */
    protected $adminScore;
    /**
     * @MongoDB\Field(type="float")
     */
    protected $localScore;
    /**
     * @MongoDB\Field(type="float")
     * @MongoDB\Index
     */
    protected $finalScore;
    /**
     * @MongoDB\Field(type="int")
     */
    protected $rankPosition;
    /**
     * @MongoDB\Field(type="date")
     * @Groups({"display"})
     */
    protected $createAt;
    /**
     * @MongoDB\Field(type="date")
     * @MongoDB\Index
     * @Groups({"display"})
     */
    protected $updateAt;

    /**
     * @deprecated
     * @MongoDB\Field(type="boolean")
     * @MongoDB\Index
     */
    protected $spotlight;
    /**
     * @MongoDB\Field(type="boolean")
     * @MongoDB\Index
     */
    protected $showAtHomepage;

    /**
     * @MongoDB\Field(type="date")
     * @MongoDB\Index
     */
    protected $expireDate;
    /**
     * @MongoDB\Field(type="boolean")
     * @MongoDB\Index
     */
    protected $softDelete;

    /**
     * @MongoDB\Field(type="collection")
     * @MongoDB\Index
     */
    protected $cities;

    /**
     * @return array key to label
     */
    public static function listOfPublishStatus()
    {
        return [
            self::STATUS_PUBLISHED => self::STATUS_PUBLISHED,
            self::STATUS_DRAFT => self::STATUS_DRAFT,
            self::STATUS_REVIEW => self::STATUS_REVIEW,
        ];
    }

    public function __construct(){
        $this->setTags(new ArrayCollection());
        $this->setImageLinks(new ArrayCollection());
        $this->setCities(new ArrayCollection());
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function updateFinalScore($localWeight = 1.0, $adminWeight = 1.0){
        $bizWeight = 1.0;
        if ($this->getMnemonoBiz()){
            $bizWeight = $this->getMnemonoBiz()->getWeighting();
        }
        $local = $this->getLocalScore();
        $admin = $this->getAdminScore();
        $finalScore = $bizWeight * ($localWeight * $local + $adminWeight * $admin);
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
     * @param MnemonoBiz $biz
     */
    public function setBizTagsCities(MnemonoBiz $biz){
        $this->setMnemonoBiz($biz);
        $this->setTags(array($biz->getCategory()));
        $this->setCities($biz->getCities());
    }

    /**
     * Get id
     *
     * @return String $id
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
     * @deprecated
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
     * @deprecated
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
     * @param DateTime $createAt
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
     * @return DateTime $createAt
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set updateAt
     *
     * @param DateTime $updateAt
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
     * @return DateTime $updateAt
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
     * @param DateTime $expireDate
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
     * @return DateTime $expireDate
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

    /**
     * Set cities
     *
     * @param collection $cities
     * @return self
     */
    public function setCities($cities)
    {
        $this->cities = $cities;
        return $this;
    }

    /**
     * Get cities
     *
     * @return collection $cities
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Set videoLinks
     *
     * @param collection $videoLinks
     * @return self
     */
    public function setVideoLinks($videoLinks)
    {
        $this->videoLinks = $videoLinks;
        return $this;
    }

    /**
     * Get videoLinks
     *
     * @return collection $videoLink
     */
    public function getVideoLinks()
    {
        return $this->videoLinks;
    }

    /**
     * Add tag
     *
     * @param string $tag
     * @return boolean
     */
    public function addTag($tag)
    {
        $tag = trim($tag);
        if (!empty($tag) &&
            !in_array($tag, $this->tags))
        {
            $this->tags[] = $tag;
            return true;
        }
        return false;
    }

    /**
     * Remove tag
     *
     * @param string $tag
     * @return boolean
     */
    public function removeTag($tag)
    {
        $key = array_search($tag, $this->tags, true);
        if ($key === false) {
            return false;
        }
        unset($this->tags[$key]);
        return true;
    }

    /**
     * Add image
     *
     * @param Image $image
     */
    public function addImage(Image $image)
    {
        $this->images[] = $image;
    }

    /**
     * Remove image
     *
     * @param Image $image
     */
    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection $images
     */
    public function getImages()
    {
        return $this->images;
    }
}

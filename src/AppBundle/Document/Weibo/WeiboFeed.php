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
 * @MongoDB\Document(collection="WeiboFeed", repositoryClass="AppBundle\Repository\Weibo\WeiboFeedRepository")
 * @ExclusionPolicy("none")
 */
class WeiboFeed {
    const WEIBO_HOST = "https://m.weibo.cn/";
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\Index
     */
    protected $mid;
    /**
     * @MongoDB\Field(type="string")
     */
    protected $text;
    /**
     * @MongoDB\Field(type="int")
     */
    protected $like_count;
    /**
     * @MongoDB\Field(type="int")
     */
    protected $comments_count;
    /**
     * @MongoDB\Field(type="int")
     */
    protected $reposts_count;
    /**
     * @MongoDB\Field(type="raw")
     */
    protected $pics;

    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\Weibo\WeiboPage")
     */
    protected $weiboPage;
    /**
     * @MongoDB\Field(type="float")
     */
    protected $created_timestamp;

    public function getPicLinks(){
        $picArray = $this->getPics();
        $linksArray = array();
        if (is_array($picArray) && !empty($picArray)){
            foreach($picArray as $pic){
                $linksArray[] = $pic["bmiddle"];
            }
        }
        return $linksArray;
    }

    public function getOriginLink(){
        $page = $this->getWeiboPage();
        if ($page instanceof WeiboPage){
            return WeiboFeed::WEIBO_HOST . $page->getUid() . "/" . $this->getMid();
        }
        return null;
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
     * Set mid
     *
     * @param string $mid
     * @return self
     */
    public function setMid($mid)
    {
        $this->mid = $mid;
        return $this;
    }

    /**
     * Get mid
     *
     * @return string $mid
     */
    public function getMid()
    {
        return $this->mid;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set likeCount
     *
     * @param int $likeCount
     * @return self
     */
    public function setLikeCount($likeCount)
    {
        $this->like_count = $likeCount;
        return $this;
    }

    /**
     * Get likeCount
     *
     * @return int $likeCount
     */
    public function getLikeCount()
    {
        return $this->like_count;
    }

    /**
     * Set commentsCount
     *
     * @param int $commentsCount
     * @return self
     */
    public function setCommentsCount($commentsCount)
    {
        $this->comments_count = $commentsCount;
        return $this;
    }

    /**
     * Get commentsCount
     *
     * @return int $commentsCount
     */
    public function getCommentsCount()
    {
        return $this->comments_count;
    }

    /**
     * Set pics
     *
     * @param raw $pics
     * @return self
     */
    public function setPics($pics)
    {
        $this->pics = $pics;
        return $this;
    }

    /**
     * Get pics
     *
     * @return raw $pics
     */
    public function getPics()
    {
        return $this->pics;
    }

    /**
     * Set weiboPage
     *
     * @param WeiboPage $weiboPage
     * @return self
     */
    public function setWeiboPage(WeiboPage $weiboPage)
    {
        $this->weiboPage = $weiboPage;
        return $this;
    }

    /**
     * Get weiboPage
     *
     * @return WeiboPage $weiboPage
     */
    public function getWeiboPage()
    {
        return $this->weiboPage;
    }

    /**
     * Set createdTimestamp
     *
     * @param float $createdTimestamp
     * @return self
     */
    public function setCreatedTimestamp($createdTimestamp)
    {
        $this->created_timestamp = $createdTimestamp;
        return $this;
    }

    /**
     * Get createdTimestamp
     *
     * @return float $createdTimestamp
     */
    public function getCreatedTimestamp()
    {
        return $this->created_timestamp;
    }

    /**
     * Set repostsCount
     *
     * @param int $repostsCount
     * @return self
     */
    public function setRepostsCount($repostsCount)
    {
        $this->reposts_count = $repostsCount;
        return $this;
    }

    /**
     * Get repostsCount
     *
     * @return int $repostsCount
     */
    public function getRepostsCount()
    {
        return $this->reposts_count;
    }
}

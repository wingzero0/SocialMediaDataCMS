<?php
/**
 * User: kit
 * Date: 15/07/15
 * Time: 20:33
 */

namespace AppBundle\Document\Facebook;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use AppBundle\Document\Facebook\FacebookPage;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Since;

/**
 * @MongoDB\Document(collection="FacebookFeed", repositoryClass="AppBundle\Repository\Facebook\FacebookFeedRepository")
 * @ExclusionPolicy("none")
 */
class FacebookFeed {
    /**
     * @MongoDB\Id
     */
    protected $id;
    /**
     * @MongoDB\Field(type="string", name="fbID")
     */
    protected $fbId;
    /**
     * @MongoDB\String
     */
    protected $message;
    /**
     * @MongoDB\Raw
     */
    protected $likes;
    /**
     * @MongoDB\Raw
     */
    protected $comments;
    /**
     * @MongoDB\Raw
     */
    protected $attachments;
    /**
     * @MongoDB\Field(type="string", name="created_time")
     */
    protected $createdTime;
    /**
     * @MongoDB\Field(type="string", name="updated_time")
     */
    protected $updatedTime;
    /**
     * @MongoDB\ReferenceOne(targetDocument="AppBundle\Document\Facebook\FacebookPage")
     */
    protected $fbPage;
    /**
     * @MongoDB\String
     */
    protected $link;
    /**
     * @MongoDB\Field(type="string", name="status_type")
     */
    protected $statusType;
    /**
     * @MongoDB\String
     */
    protected $story;
    /**
     * @MongoDB\String
     */
    protected $type;
    /**
     * @MongoDB\String
     */
    protected $source;

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

    public function getShortLink(){
        return "https://www.facebook.com/" . $this->getFbId();
    }

    public function getGuessLink(){
        if ($this->getStatusType() == "added_video"){
            $this->getLink();
        }
        $story = $this->getStory();
        if (!$story){
            return $this->getShortLink();
        }

        $pattern = "/new photos to the album:/";
        $ret = preg_match($pattern, $story);
        if ($ret > 0){
            return $this->getLink();
        }

        $pattern = "/cover photo./";
        $ret = preg_match($pattern, $story);
        if ($ret > 0){
            return $this->getLink();
        }

        return $this->getShortLink();
    }

    /**
     * Set message
     *
     * @param string $message
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     *
     * @return string $message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set likes
     *
     * @param raw $likes
     * @return self
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
        return $this;
    }

    /**
     * Get likes
     *
     * @return raw $likes
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @return int
     */
    public function getLikesTotalCount()
    {
        $likesRaw = $this->getLikes();
        if (isset($likesRaw["summary"]) && isset($likesRaw["summary"]["total_count"])){
            return intval($likesRaw["summary"]["total_count"]);
        }
        return 0;
    }

    /**
     * Set comments
     *
     * @param raw $comments
     * @return self
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * Get comments
     *
     * @return raw $comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return int
     */
    public function getCommentsTotalCount()
    {
        $commentsRaw = $this->getComments();
        if (isset($commentsRaw["summary"]) && isset($commentsRaw["summary"]["total_count"])){
            return intval($commentsRaw["summary"]["total_count"]);
        }
        return 0;
    }

    /**
     * Set fbPage
     *
     * @param FacebookPage $fbPage
     * @return self
     */
    public function setFbPage(FacebookPage $fbPage)
    {
        $this->fbPage = $fbPage;
        return $this;
    }

    /**
     * Get fbPage
     *
     * @return FacebookPage $fbPage
     */
    public function getFbPage()
    {
        return $this->fbPage;
    }

    /**
     * Set createdTime
     *
     * @param string $createdTime
     * @return self
     */
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;
        return $this;
    }

    /**
     * Get createdTime
     *
     * @return string $createdTime
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    /**
     * Set attachments
     *
     * @param raw $attachments
     * @return self
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * Get attachments
     *
     * @return raw $attachments
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    public function getAttachmentImageURL(){
        $attachments = $this->getAttachments();
        if (!$attachments){
            return array();
        }
        if (
            isset($attachments["data"])
            && is_array($attachments["data"])
            && isset($attachments["data"][0]["type"])
        ){
            if (
                $attachments["data"][0]["type"] == "album"
                && isset($attachments["data"][0]["subattachments"])
            ){
                return $this->parseSubAttachmentImageURL($attachments["data"][0]["subattachments"]);
            }else if (
                ($attachments["data"][0]["type"] == "photo"
                    || $attachments["data"][0]["type"] == "share"
                    || $attachments["data"][0]["type"] == "video_inline")
                && isset($attachments["data"][0]["media"])
            ){
                $url = $this->parseMediaImageURL($attachments["data"][0]["media"]);
                if ($url){
                    return array($url);
                }
            }
        }
        return array();
    }

    /**
     * @return array
     */
    public function getVideoURLs(){
        $ret = array();
        if ($this->getType() == "video"){
            $source = $this->getSource();
            if (!empty($source)){
                $ret[] = $this->getSource();
            }
        }
        return $ret;
    }

    /**
     * @param array $subAttachments
     * @return array $imageURLs
     */
    private function parseSubAttachmentImageURL($subAttachments){
        $imageURLs = array();
        if (
            isset($subAttachments["data"])
            && is_array($subAttachments["data"])
        ){
            foreach($subAttachments["data"] as $data){
                if (
                    isset($data["type"])
                    && $data["type"] == "photo"
                    && isset($data["media"])
                ){
                    $imageURL = $this->parseMediaImageURL($data["media"]);
                    $imageURLs[] = $imageURL;
                }
            }
        }

        return $imageURLs;
    }

    /**
     * @param array $media
     * @return string
     */
    private function parseMediaImageURL($media){
        if (
            isset($media["image"])
            && isset($media["image"]["src"])
        ){
            return $media["image"]["src"];
        }
        return null;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return self
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Get link
     *
     * @return string $link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set statusType
     *
     * @param string $statusType
     * @return self
     */
    public function setStatusType($statusType)
    {
        $this->statusType = $statusType;
        return $this;
    }

    /**
     * Get statusType
     *
     * @return string $statusType
     */
    public function getStatusType()
    {
        return $this->statusType;
    }

    /**
     * Set story
     *
     * @param string $story
     * @return self
     */
    public function setStory($story)
    {
        $this->story = $story;
        return $this;
    }

    /**
     * Get story
     *
     * @return string $story
     */
    public function getStory()
    {
        return $this->story;
    }

    /**
     * Set updatedTime
     *
     * @param string $updatedTime
     * @return self
     */
    public function setUpdatedTime($updatedTime)
    {
        $this->updatedTime = $updatedTime;
        return $this;
    }

    /**
     * Get updatedTime
     *
     * @return string $updatedTime
     */
    public function getUpdatedTime()
    {
        return $this->updatedTime;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return self
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get source
     *
     * @return string $source
     */
    public function getSource()
    {
        return $this->source;
    }
}

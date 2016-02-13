<?php
/**
 * User: kit
 * Date: 05/12/15
 * Time: 9:04 PM
 */
namespace Mnemono\BackgroundServiceBundle\Services;

use Mnemono\BackgroundServiceBundle\Services\BaseService;
use Mmoreram\GearmanBundle\Driver\Gearman;
use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\Facebook\FacebookMeta;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\Post;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use Symfony\Component\Config\Definition\Exception\Exception;
use AppBundle\Utility\GearmanServiceName;

/**
 * @Gearman\Work(
 *     iterations = 1000,
 *     description = "synchronize fb feed to mnenmono post",
 *     defaultMethod = "doBackground",
 *     service="SyncFbFeedService"
 * )
 */
class SyncFbFeedService extends BaseService{
    /**
     * Job for create post form fbID
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     iterations = 1000,
     *     name = "createPost",
     *     description = "Create post"
     * )
     */
    public function createPost(\GearmanJob $job){
        try {
            $key_json = json_decode($job->workload(), true);
            $fbId = $key_json["fbId"];
            $this->resetDM();
            $post = $this->createPostByFbId($fbId);
            return true;
        }catch (\Exception $e){
            $this->logExecption($e);
            exit(-1);
        }
    }

    /**
     * Job for update post form fbID
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     iterations = 1000,
     *     name = "updatePost",
     *     description = "update post"
     * )
     */
    public function updatePost(\GearmanJob $job){
        try{
            $key_json = json_decode($job->workload(), true);
            $fbId = $key_json["fbId"];
            $this->resetDM();
            $post = $this->updatePostByFbId($fbId);
            return true;
        }catch (\Exception $e){
            $this->logExecption($e);
            exit(-1);
        }
    }

    /**
     * Job for remove post by id
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     iterations = 1000,
     *     name = "removePost",
     *     description = "remove post"
     * )
     */
    public function removePost(\GearmanJob $job){
        try{
            $key_json = json_decode($job->workload(), true);
            $id = $key_json["id"];
            $this->resetDM();
            $this->removePostById($id);
            return true;
        }catch (\Exception $e){
            $this->logExecption($e);
            exit(-1);
        }
    }

    /**
     * @param string $fbId
     * @return Post|null
     */
    private function createPostByFbId($fbId){
        $feed = $this->queryFeedByFbId($fbId);
        if ($feed instanceof FacebookFeed){
            $post = $this->createPostByFeed($feed);
            if ($post != null){$this->persistPost($post);}
            return $post;
        }
        return null;
    }

    /**
     * @param string $fbId
     * @return Post|null
     */
    private function updatePostByFbId($fbId){
        $feed = $this->queryFeedByFbId($fbId);
        if (!($feed instanceof FacebookFeed)){
            $this->logError("FacebookFeed of fbID " . $fbId . " not found");
            return null;
        }
        $post = $this->queryPostByFeed($feed);
        if (!($post instanceof Post)){
            $this->logError("Post of FacebookFeed ID: " . $feed->getId() . " fbID " . $fbId . " not found");
            return null;
        }
        return $this->updatePostByRef($post);
    }


    /**
     * @param Post $post
     * @return Post
     */
    private function updatePostByRef(Post $post){
        $ref = $post->getImportFromRef();
        if ($ref instanceof FacebookFeed){
            $post->setContent($ref->getMessage());
            $updatedTime = \DateTime::createFromFormat(\DateTime::ISO8601, $ref->getUpdatedTime());
            $post->setUpdateAt($updatedTime);
            $post->setMeta($this->fbMetaBuilder($ref));
            $this->persistPost($post);
        }
        return $post;
    }

    /**
     * @param string $id
     * @return null
     */
    private function removePostById($id){
        $post = $this->getPostRepo()->find($id);
        if (!($post instanceof Post)){
            $this->logError("Post:" . $id . " not found");
            return null;
        }

        $dm = $this->getDM();
        $dm->remove($post);
        $dm->flush();
        return null;
    }

    /**
     * @param $fbId
     * @return FacebookFeed|null
     */
    private function queryFeedByFbId($fbId){
        return $this->getFbFeedRepo()->findOneByFbId($fbId);
    }

    /**
     * @param FacebookFeed $feed
     * @return Post|null
     */
    private function queryPostByFeed(FacebookFeed $feed){
        return $this->getPostRepo()->findOneByFeed($feed);
    }

    private function persistPost(Post $post){
        $dm = $this->getDM();
        $biz = $post->getMnemonoBiz();
        $timing = new \DateTime();
        if ($biz instanceof MnemonoBiz) {
            $biz->setLastPostUpdateAt($timing);
            $dm->persist($biz);
            $dm->persist($post);
            $dm->flush();
            $dm->clear();
        }else{
            $msg = sprintf("post %d has no biz", $post->getImportFromRef()->getId());
            $this->logError($msg);
        }
    }

    /**
     * @param FacebookFeed $feed
     * @return Post|null
     */
    private function createPostByFeed(FacebookFeed $feed){
        $fbPage = $feed->getFbPage();
        if ($fbPage->getExcpetion() == true){
            return null;
        }

        $post = $this->queryPostByFeed($feed);
        if ($post != null){
            return null;
        }
        $post = new Post();
        $post->setImportFrom("facebookFeed");
        $post->setImportFromRef($feed);
        $post->setContent($feed->getMessage());
        $post->setPublishStatus("review");
        $post->setOriginalLink($feed->getGuessLink());
        $post->setImageLinks($feed->getAttachmentImageURL());
        $createDate = \DateTime::createFromFormat(\DateTime::ISO8601, $feed->getCreatedTime());
        $post->setCreateAt($createDate);
        $updateDate = \DateTime::createFromFormat(\DateTime::ISO8601, $feed->getUpdatedTime());
        $post->setUpdateAt($updateDate);
        $expireDate = clone $createDate;
        $expireDate->add(new \DateInterval("P7D"));
        $post->setExpireDate($expireDate);
        $meta = $this->fbMetaBuilder($feed);
        $post->setMeta($meta);
        $biz = $this->getMnemenoBizRepo()->findOneByFbPage($feed->getFbPage());

        if ($biz instanceof MnemonoBiz){
            $post->setMnemonoBiz($biz);
            $tags = array($biz->getCategory(), $biz->getLocation()->getCity());
            $post->setTags($tags);
        }else{
            $msg = sprintf("biz not found: feed fbID :%s, page fbID: %s", $feed->getFbId() ,$fbPage->getFbId());
            $this->logError($msg);
        }
        return $post;
    }
    private function fbMetaBuilder(FacebookFeed $feed){
        $meta = new FacebookMeta();
        $likes = $feed->getLikes();
        $comments = $feed->getComments();
        $meta->setFbId($feed->getFbId());

        $likeCount = 0;
        if (isset($likes["summary"]) && isset($likes["summary"]["total_count"])){
            $likeCount = $likes["summary"]["total_count"];
        }
        $meta->setFbTotalLikes($likeCount);

        $commentCount = 0;
        if (isset($comments["summary"]) && isset($comments["summary"]["total_count"])){
            $commentCount = $comments["summary"]["total_count"];
        }
        $meta->setFbTotalComments($commentCount);
        return $meta;
    }
}
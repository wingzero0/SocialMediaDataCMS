<?php
/**
 * User: kit
 * Date: 05/12/15
 * Time: 9:04 PM
 */
namespace AppBundle\Services;

use AppBundle\Services\BaseService;
use Mmoreram\GearmanBundle\Driver\Gearman;
use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\Facebook\FacebookMeta;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\Post;

/**
 * @Gearman\Work(
 *     iterations = 10,
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
     *     iterations = 10,
     *     name = "createPost",
     *     description = "Create post"
     * )
     */
    public function createPost(\GearmanJob $job){
        $key_json = json_decode($job->workload(), true);
        $fbId = $key_json["fbId"];
        $this->resetDM();
        $this->createPostByFbId($fbId);

        return true;
    }

    /**
     * Job for update post form fbID
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     iterations = 10,
     *     name = "updatePost",
     *     description = "update post"
     * )
     */
    public function updatePost(\GearmanJob $job){
        $key_json = json_decode($job->workload(), true);
        $fbId = $key_json["fbId"];
        $this->resetDM();
        $this->updatePostByFbId($fbId);

        return true;
    }

    /**
     * @param string $fbId
     */
    private function createPostByFbId($fbId){
        $feed = $this->queryFeedByFbId($fbId);
        if ($feed instanceof FacebookFeed){
            $post = $this->createPostByFeed($feed);
            if ($post != null){$this->persistPost($post);}
        }
    }

    /**
     * @param string $fbId
     */
    private function updatePostByFbId($fbId){
        $feed = $this->queryFeedByFbId($fbId);
        $post = $this->queryPostByFeed($feed);
        $this->updatePostByRef($post);
    }


    /**
     * @param Post $post
     */
    private function updatePostByRef(Post $post){
        $ref = $post->getImportFromRef();
        if ($ref instanceof FacebookFeed){
            $post->setContent($ref->getMessage());
            $post->setOriginalLink($ref->getShortLink());
            $post->setMeta($this->fbMetaBuilder($ref));
            $this->persistPost($post);
        }
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
        $timing = new \DateTime();
        if (!$post->getId()){
            $post->setCreateAt($timing);
        }
        $post->setUpdateAt($timing);
        $biz = $post->getMnemonoBiz();
        if (!$biz instanceof MnemonoBiz) {
            var_dump($post->getImportFromRef()->getId());
        }
        $biz->setLastPostUpdateAt($timing);
        $dm->persist($biz);
        $dm->persist($post);
        $dm->flush();
        $dm->clear();
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
        $post->setOriginalLink($feed->getShortLink());
        $meta = $this->fbMetaBuilder($feed);
        $post->setMeta($meta);
        $biz = $this->getMnemenoBizRepo()->findOneByFbPage($feed->getFbPage());

        if ($biz instanceof MnemonoBiz){
            $post->setMnemonoBiz($biz);
            $post->addTag($biz->getCategory());
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
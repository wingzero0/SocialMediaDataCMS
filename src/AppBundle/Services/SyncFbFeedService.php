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
use Mmoreram\GearmanBundle\Service\GearmanClient;
use Symfony\Component\Config\Definition\Exception\Exception;

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
            $this->updateScore($post);
            return true;
        }catch (\Exception $e){
            echo $e->getMessage()."\n";
            echo $e->getTraceAsString()."\n";
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
            $this->updateScore($post);
            return true;
        }catch (\Exception $e){
            echo $e->getMessage()."\n";
            echo $e->getTraceAsString()."\n";
            exit(-1);
        }
    }

    /**
     * @param Post $post
     */
    private function updateScore($post){
        if ($post instanceof Post){
            $json = json_encode(array("id" => $post->getId()));
            $this->getGearman()->doBackgroundJob('AppBundleServicesPostScoreService~updateScore', $json);
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
     * @return Post
     */
    private function updatePostByFbId($fbId){
        $feed = $this->queryFeedByFbId($fbId);
        $post = $this->queryPostByFeed($feed);
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
            $post->setOriginalLink($ref->getShortLink());
            $post->setMeta($this->fbMetaBuilder($ref));
            $this->persistPost($post);
        }
        return $post;
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
        $currentDate = new \DateTime();
        $post->setExpireDate($currentDate->add(new \DateInterval("P7D")));
        $meta = $this->fbMetaBuilder($feed);
        $post->setMeta($meta);
        $biz = $this->getMnemenoBizRepo()->findOneByFbPage($feed->getFbPage());

        if ($biz instanceof MnemonoBiz){
            $post->setMnemonoBiz($biz);
            $post->setTags(array($biz->getCategory()));
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
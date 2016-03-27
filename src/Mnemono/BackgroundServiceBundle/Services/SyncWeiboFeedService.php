<?php
/**
 * User: kit
 * Date: 20/03/16
 * Time: 5:49 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services;

use AppBundle\Document\Weibo\WeiboFeed;
use AppBundle\Document\Weibo\WeiboMeta;
use Mmoreram\GearmanBundle\Driver\Gearman;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\Post;


/**
 * @Gearman\Work(
 *     iterations = 1000,
 *     description = "synchronize weibo feed to mnenmono post",
 *     defaultMethod = "doBackground",
 *     service="SyncWeiboFeedService"
 * )
 */
class SyncWeiboFeedService extends BaseService {
    private $cachedBiz;
    /**
     * Job for create post form weiboID
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
            $mid = $key_json["mid"];
            $this->resetDM();
            $post = $this->createPostByMid($mid);
            return true;
        }catch (\Exception $e){
            $this->logExecption($e);
            exit(-1);
        }
    }

    /**
     * @param string $mid
     * @return Post|null
     */
    private function createPostByMid($mid){
        $feed = $this->getWeiboFeedRepo()->findOneByMid($mid);
        if ($feed instanceof WeiboFeed){
            $post = $this->createPostByFeed($feed);
            if ($post != null){
                $this->persistPost($post);
            }
            return $post;
        }
        return null;
    }

    /**
     * @param WeiboFeed $feed
     * @return Post|null
     */
    private function createPostByFeed(WeiboFeed $feed){
        $ret = $this->newPostChecking($feed);
        if ($ret == false){
            return null;
        }

        $post = new Post();
        $post->setImportFrom(Post::importFromWeibo);
        $post->setImportFromRef($feed);
        $post->setContent($feed->getText());
        $post->setPublishStatus(Post::statusReview);
        $createdDate = new \DateTime();
        $createdDate->setTimestamp($feed->getCreatedTimestamp());
        $post->setCreateAt($createdDate);
        $post->setMeta($this->metaBuilder($feed));

        $biz = $this->getCachedBiz();
        $post->setBizTagsCities($biz);
        $post->setTags(array($biz->getCategory()));
        return $post;
    }

    /**
     * checking feed and page property, also query related MnemonoBiz
     * and store it in $cachedBiz
     *
     * @param WeiboFeed $feed
     * @return bool If any invalid value found, function return false. if no invalid value found, function return true and set latest MnemonoBiz to $cachedBiz
     */
    private function newPostChecking(WeiboFeed $feed){
        $weiboPage = $feed->getWeiboPage();
        if ($weiboPage->getException() == true){
            $msg = sprintf("weibo page exception: uid " . $weiboPage->getUid());
            $this->logError($msg);
            return false;
        }

        $post = $this->queryPostByFeed($feed);
        if ($post != null){
            $msg = sprintf("duplicated post:weiboFeed mid " . $feed->getMid());
            $this->logError($msg);
            return false;
        }

        $biz = $this->getMnemenoBizRepo()->findOneByWeiboPage($weiboPage);

        if (!($biz instanceof MnemonoBiz)){
            $msg = sprintf("biz not found: weiboFeed mid :%s, weiboPage uid: %s", $feed->getMid() , $weiboPage->getUid());
            $this->logError($msg);
            return false;
        }else{
            $this->setCachedBiz($biz);
        }
        return true;
    }

    /**
     * @param WeiboFeed $feed
     * @return Post|null
     */
    private function queryPostByFeed(WeiboFeed $feed){
        return $this->getPostRepo()->findOneByWeiboFeed($feed);
    }

    /**
     * @param WeiboFeed $feed
     * @return WeiboMeta
     */
    private function metaBuilder(WeiboFeed $feed){
        $meta = new WeiboMeta();
        $meta->setCommentsCount($feed->getCommentsCount());
        $meta->setLikeCount($feed->getLikeCount());
        $meta->setRepostsCount($feed->getRepostsCount());
        return $meta;
    }

    /**
     * @param Post $post
     */
    private function persistPost(Post $post){
        $this->getDM()->persist($post);
        $this->getDM()->flush();
    }

    /**
     * @return MnemonoBiz
     */
    private function getCachedBiz()
    {
        return $this->cachedBiz;
    }

    /**
     * @param MnemonoBiz $cachedBiz
     */
    private function setCachedBiz(MnemonoBiz $cachedBiz = null)
    {
        $this->cachedBiz = $cachedBiz;
    }
}
<?php
/**
 * User: kit
 * Date: 20/03/16
 * Time: 5:49 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services;

use AppBundle\Document\Weibo\WeiboFeed;
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
            if ($post != null){$this->persistPost($post);}
            return $post;
        }
        return null;
    }

    /**
     * @param WeiboFeed $feed
     * @return Post|null
     */
    private function createPostByFeed(WeiboFeed $feed){
        $weiboPage = $feed->getWeiboPage();
        if ($weiboPage->getExcpetion() == true){
            return null;
        }

        $post = $this->queryPostByFeed($feed);
        if ($post != null){
            return null;
        }
        $post = new Post();
        $post->setImportFrom("weiboFeed");
        $post->setImportFromRef($feed);
        $post->setContent($feed->getText());
        $post->setPublishStatus("review");
        $post->setCreateAt();
    }

    /**
     * @param WeiboFeed $feed
     * @return Post|null
     */
    private function queryPostByFeed(WeiboFeed $feed){
        return $this->getPostRepo()->findOneByWeiboFeed($feed);
    }
}
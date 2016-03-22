<?php
/**
 * User: kit
 * Date: 20/03/16
 * Time: 5:49 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services;

use Mmoreram\GearmanBundle\Driver\Gearman;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\Post;
use AppBundle\Utility\GearmanServiceName;


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
            $post = $this->createPostByFbId($mid);
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
}
<?php
/**
 * User: kit
 * Date: 05/12/15
 * Time: 9:04 PM
 */
namespace AppBundle\Services;

use AppBundle\Services\BaseService;
use Mmoreram\GearmanBundle\Driver\Gearman;

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
        echo 'Job testA done!' . PHP_EOL;

        return true;
    }
}
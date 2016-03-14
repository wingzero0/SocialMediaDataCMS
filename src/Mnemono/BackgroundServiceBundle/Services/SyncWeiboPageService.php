<?php
/**
 * User: kit
 * Date: 14/03/16
 * Time: 6:46 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services;

use AppBundle\Document\Weibo\WeiboPage;
use Mmoreram\GearmanBundle\Driver\Gearman;
use AppBundle\Document\MnemonoBiz;

/**
 * @Gearman\Work(
 *     iterations = 1000,
 *     description = "synchronize weibo Page to mnenmono biz",
 *     defaultMethod = "doBackground",
 *     service="SyncWeiboPageService"
 * )
 */
class SyncWeiboPageService extends BaseService{
    /**
     * Job for create post form fbID
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     iterations = 1000,
     *     name = "createBiz",
     *     description = "Create Biz (page)"
     * )
     */
    public function createBiz(\GearmanJob $job){
        try {
            $key_json = json_decode($job->workload(), true);
            $uid = $key_json["uid"];
            $this->createBizByUid($uid);
            return true;
        }catch (\Exception $e){
            $this->logExecption($e);
            exit(-1);
        }
    }

    /**
     * @param string $uid
     * @return null|MnemonoBiz
     */
    private function createBizByUid($uid){
        $page = $this->getWeiboPageRepo()->findOneByUid($uid);
        if (!$page instanceof WeiboPage){
            $this->logError($uid . ": Page is not WeiboPage");
            return null;
        }
        $biz = $this->getMnemenoBizRepo()->findOneByWeiboPage($page);
        if ($biz != null){
            $this->logError($uid . ": biz is not null (biz already exists)");
            return $biz;
        }else{
            $biz = $this->bizBuilder($page);
            $dm = $this->getDM();
            $dm->persist($biz);
            $dm->flush();
            $dm->clear();
        }

        return null;
    }

    /**
     * @param WeiboPage $page
     * @return MnemonoBiz
     */
    private function bizBuilder(WeiboPage $page){
        $cities = array();
        if ($page->getCity()){
            $cities[] = $page->getCity();
        }
        $biz = new MnemonoBiz();
        $biz->setName($page->getName())
            ->setCities($cities)
            ->setImportFrom("weiboPage")
            ->setImportFromRef($page)
            ->setLastModDate(new \DateTime());
        ;
        return $biz;
    }
}
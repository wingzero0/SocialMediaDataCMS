<?php
/**
 * User: kit
 * Date: 22/12/15
 * Time: 8:35 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services;

use Mnemono\BackgroundServiceBundle\Services\BaseService;
use Mmoreram\GearmanBundle\Driver\Gearman;
use AppBundle\Document\Facebook\FacebookPage;
use AppBundle\Document\MnemonoBiz;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use Symfony\Component\Config\Definition\Exception\Exception;


/**
 * @Gearman\Work(
 *     iterations = 1000,
 *     description = "synchronize fb Page to mnenmono biz",
 *     defaultMethod = "doBackground",
 *     service="SyncFbPageService"
 * )
 */
class SyncFbPageService extends BaseService{
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
            $fbId = $key_json["fbId"];
            $this->createBizByFbId($fbId);
            return true;
        }catch (\Exception $e){
            $this->logExecption($e);
            exit(-1);
        }
    }

    /**
     * Job for create post form fbID
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     iterations = 1000,
     *     name = "updateBiz",
     *     description = "Update Biz (page)"
     * )
     */
    public function updateBiz(\GearmanJob $job){
        try {
            $key_json = json_decode($job->workload(), true);
            $fbId = $key_json["fbId"];
            $this->updateBizByFbId($fbId);
            return true;
        }catch (\Exception $e){
            $this->logExecption($e);
            exit(-1);
        }
    }

    private function updateBizByFbId($fbId){
        $page = $this->queryPageByFbId($fbId);

        $pageRaw = $this->queryPageRawByFbId($fbId);

        $biz = $this->queryBizByFbPage($page);

        if ($biz instanceof MnemonoBiz && $page instanceof FacebookPage){
            $dm = $this->getDM();
            $biz = $this->updateBizByRaw($biz, $page, $pageRaw);
            $dm->persist($biz);
            $dm->flush();
            $dm->clear();
        }


        return $biz;
    }
    private function createBizByFbId($fbId){
        $page = $this->queryPageByFbId($fbId);
	//TODO refind the checking flow and logic, check the return logic
	if ($page == null){
            $this->logError($fbId . ": Page is not FacebookPage");
	    return null;	
	}

        $pageRaw = $this->queryPageRawByFbId($fbId);

        $biz = $this->queryBizByFbPage($page);

        if ($biz == null && $page instanceof FacebookPage){
            $biz = $this->bizBuilder($page, $pageRaw);
            $dm = $this->getDM();
            $dm->persist($biz);
            $dm->flush();

            $dm->clear();
        }else{
            $this->logError($fbId . ": biz is not null or Page is not FacebookPage");
        }


        return $biz;
    }

    /**
     * @param FacebookPage $page
     * @param array $pageRaw
     * @return MnemonoBiz
     */
    private function bizBuilder(FacebookPage $page, $pageRaw){
        $biz = new MnemonoBiz();
        return $this->updateBizByRaw($biz, $page, $pageRaw);
    }

    /**
     * @param MnemonoBiz $biz
     * @param FacebookPage $page
     * @param $pageRaw
     * @return MnemonoBiz
     */
    private function updateBizByRaw(MnemonoBiz $biz, FacebookPage $page, $pageRaw){
        $websites = array();
        if (isset($pageRaw['link'])){
            $websites[] = $pageRaw['link'];
        }
        if (isset($pageRaw['website'])){
            $websites[] = $pageRaw['website'];
        }
        if (!empty($websites)){
            $biz->setWebsites($websites);
        }
        if (isset($pageRaw["mnemono"]) && isset($pageRaw["mnemono"]["category"])){
            $biz->setCategory($pageRaw["mnemono"]["category"]);
        }
        list($address, $city) = $this->createLocation($pageRaw);
        $biz->setAddresses(array($address))
            ->setCities(array($city))
            ->setName($pageRaw["name"])
            ->setImportFrom("facebookPage")
            ->setImportFromRef($page)
            ->setLastModDate(new \DateTime());
        return $biz;
    }

    /**
     * @param $pageRaw
     * @return array
     */
    private function createLocation($pageRaw){
        $city = null;
        $address = null;
        if (isset($pageRaw["mnemono"])){
            if (isset($pageRaw["mnemono"]["location"]["city"])){
                $city = $pageRaw["mnemono"]["location"]["city"];
            }
        }

        if (isset($pageRaw["location"])){
            $address = (isset($pageRaw["location"]["street"]) ? $pageRaw["location"]["street"] : null);
            if ($city == null){
                $city = (isset($pageRaw["location"]["city"]) ? $pageRaw["location"]["city"] : null);
            }
        }
        return array($address, $city);
    }

    /**
     * @param string $fbId
     * @return FacebookPage|null
     */
    private function queryPageByFbId($fbId)
    {
        return $this->getFacebookPageRepo()->findOneByFbId($fbId);
    }

    /**
     * @param string $fbId
     * @return array|null
     */
    private function queryPageRawByFbId($fbId)
    {
        return $this->getFacebookPageRepo()->findOneRawByFbId($fbId);
    }

    /**
     * @param FacebookPage $page
     * @return mixed
     */
    private function queryBizByFbPage(FacebookPage $page)
    {
        return $this->getMnemenoBizRepo()->findOneByFbPage($page);
    }
}

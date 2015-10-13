<?php
/**
 * Created by PhpStorm.
 * User: kitlei
 * Date: 7/9/2015
 * Time: 9:35 AM
 */

namespace AppBundle\Command;

use AppBundle\Document\Facebook\FacebookPage;
use AppBundle\Document\Location;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Repository\Facebook\FacebookPageRepository;
use AppBundle\Command\BaseCommand;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncFbPageToBizCommand extends BaseCommand{
    protected function configure(){
        $this->setName("mnemono:sync:fbpagetobiz")
            ->setDescription("sync facebook page to biz")
            ->addOption('action', null,
                InputOption::VALUE_OPTIONAL,
                'over write current biz value with facebook page',
                'createFromFbCollection')
            ->addOption('fbId', null ,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'the specific fbId you want to sync')
            ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $action = $input->getOption('action');
        if ($action == "createFromFbCollection"){
            $this->createBizFromFbPageCollection();
        }else if ($action == "updateFromFbCollection"){
            $this->updateBizFromFbPageCollection();
        }else if ($action == "createFromFb"){
            $fbIds = $input->getOption('fbId');
            if (!empty($fbIds)){
                foreach ($fbIds as $fbId){
                    $this->createBizByFbPage($fbId);
                }
            }else{
                $output->writeln("no fbId");
            }
        }else if ($action == "updateFromFb"){
            $fbIds = $input->getOption('fbId');
            if (!empty($fbIds)){
                foreach ($fbIds as $fbId){
                    $this->updateBizByFbPage($fbId);
                }
            }else{
                $output->writeln("no fbId");
            }
        }

    }
    private function updateBizFromFbPageCollection(){
        $this->loopFbPageCollection(
            function (FacebookPage $page){
                $this->updateBizByFbPage($page->getFbId());
            }
        );
    }
    private function createBizFromFbPageCollection(){
        $this->loopFbPageCollection(
            function(FacebookPage $page){
                $this->createBizByFbPage($page->getFbId());
            }
        );

    }

    private function loopFbPageCollection($callBack){
        $limit = 100;
        $lastPageId = null;
        $firstRun = true;

        do{
            $this->resetDM();
            $qb = $this->getFbPageRepo()->getQueryBuilder($limit);

            if (!$firstRun){
                $qb->field("id")->gt($lastPageId);
            }
            $pages = $qb->getQuery()->execute();

            $newPageCount = $pages->count(true);

            foreach($pages as $page){
                if ($page instanceof FacebookPage){
                    $callBack($page);
                    $lastPageId = $page->getId();
                }else{
                    $newPageCount = -1;
                }
            }
            $firstRun = false;
        }while($newPageCount > 0);
    }

    private function updateBizByFbPage($fbId){
        $page = $this->queryPageByFbId($fbId);

        $pageRaw = $this->queryPageRawByFbId($fbId);

        $biz = $this->queryBizByFbPage($page);

        if ($biz instanceof MnemonoBiz && $page instanceof FacebookPage){
            $dm = $this->getDM();
            $biz = $this->updateBiz($biz, $page, $pageRaw);
            $dm->persist($biz);
            $dm->flush();
            $dm->clear();
        }


        return $biz;
    }
    private function createBizByFbPage($fbId){
        $page = $this->queryPageByFbId($fbId);

        $pageRaw = $this->queryPageRawByFbId($fbId);

        $biz = $this->queryBizByFbPage($page);

        if ($biz == null && $page instanceof FacebookPage){
            $biz = $this->bizBuilder($page, $pageRaw);
            $dm = $this->getDM();
            $dm->persist($biz);
            $dm->flush();

            $dm->clear();
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
        return $this->updateBiz($biz, $page, $pageRaw);
    }

    /**
     * @param MnemonoBiz $biz
     * @param FacebookPage $page
     * @param $pageRaw
     * @return MnemonoBiz
     */
    private function updateBiz(MnemonoBiz $biz, FacebookPage $page, $pageRaw){
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
        $location = $this->createLocation($pageRaw);
        $biz->setLocation($location)
            ->setName($pageRaw["name"])
            ->setImportFrom("facebookPage")
            ->setImportFromRef($page)
            ->setLastModDate(new \DateTime());
        return $biz;
    }

    /**
     * @param $pageRaw
     * @return Location
     */
    private function createLocation($pageRaw){
        $city = null;
        $country = null;
        $street = null;
        if (isset($pageRaw["mnemono"])){
            if (isset($pageRaw["mnemono"]["location"]["city"])){
                $city = $pageRaw["mnemono"]["location"]["city"];
            }
            if (isset($pageRaw["mnemono"]["location"]["country"])){
                $country = $pageRaw["mnemono"]["location"]["country"];
            }
        }

        if (isset($pageRaw["location"])){
            $street = (isset($pageRaw["location"]["street"]) ? $pageRaw["location"]["street"] : null);
            if ($city == null){
                $city = (isset($pageRaw["location"]["city"]) ? $pageRaw["location"]["city"] : null);
            }
            if ($country == null){
                $country = (isset($pageRaw["location"]["country"]) ? $pageRaw["location"]["country"] : null);
            }
        }

        $location = new Location();
        $location->setCity($city)
            ->setCountry($country)
            ->setAddress($street);
        $this->getDM()->persist($location);
        return $location;
    }

    /**
     * @param string $fbId
     * @return FacebookPage|null
     */
    private function queryPageByFbId($fbId)
    {
        $dm = $this->getDM();
        $page = $dm->createQueryBuilder($this->facebookPageDocumentPath)
            ->field("fbId")->equals($fbId)
            ->getQuery()->getSingleResult();
        return $page;
    }

    /**
     * @param string $fbId
     * @return array
     */
    private function queryPageRawByFbId($fbId)
    {
        $dm = $this->getDM();
        $pageRaw = $dm->createQueryBuilder($this->facebookPageDocumentPath)
            ->hydrate(false)
            ->field("fbId")->equals($fbId)
            ->getQuery()->getSingleResult();
        return $pageRaw;
    }

    /**
     * @param FacebookPage $page
     * @return mixed
     */
    private function queryBizByFbPage(FacebookPage $page)
    {
        $dm = $this->getDM();
        $biz = $dm->createQueryBuilder($this->mnemonoBizDocumentPath)
            ->field("importFrom")->equals("facebookPage")
            ->field("importFromRef")->references($page)
            ->getQuery()->getSingleResult();
        return $biz;
    }

    /**
     * @return FacebookPageRepository
     */
    private function getFbPageRepo(){
        return $this->getDM()->getRepository($this->facebookPageDocumentPath);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: kitlei
 * Date: 7/9/2015
 * Time: 9:35 AM
 */

namespace AppBundle\Command;

use AppBundle\Document\FacebookPage;
use AppBundle\Document\Location;
use AppBundle\Document\MnemonoBiz;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncFbPageToBizCommand extends ContainerAwareCommand{
    protected function configure(){
        $this->setName("mnemono:sync:fbpagetobiz")
            ->setDescription("sync facebook page to biz")
            ->addOption('action', null,
                InputOption::VALUE_OPTIONAL,
                'over write current biz value with facebook page',
                'dumpFromFb')
            ->addOption('fbId', null ,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'the specific fbId you want to sync')
            ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $fbIds = $input->getOption('fbId');
        $action = $input->getOption('action');
        if ($action == "dumpFromFb"){
            $this->createBizFromFbPageCollection();
        }else if (!empty($fbIds)){
            foreach ($fbIds as $fbId){
                if ($action=="createFromFb"){
                    $this->createBizByFbPage($fbId);
                }
            }
        }else{
            $output->writeln("no fbId");
        }
    }
    private function createBizFromFbPageCollection(){
        $dm = $this->getDM();
        $skip = 0;
        $limit = 10;
        do{
            $pages = $dm->createQueryBuilder("AppBundle:FacebookPage")
                ->field("exception")->notEqual(true)
                ->limit($limit)->skip($skip)
                ->getQuery()->execute();
            $newAdd = $pages->count(true);
            if ($pages->count() > 0){
                foreach($pages as $page){
                    if ($page instanceof FacebookPage){
                        $this->createBizByFbPage($page->getFbId());
                    }
                    $skip++;
                }
            }
        }while($newAdd > 0);
    }
    private function createBizByFbPage($fbId){
        $dm = $this->getDM();
        $page = $dm->createQueryBuilder("AppBundle:FacebookPage")
            //->hydrate(false)
            ->field("fbId")->equals($fbId)
            ->getQuery()->getSingleResult();

        $pageRaw = $dm->createQueryBuilder("AppBundle:FacebookPage")
            ->hydrate(false)
            ->field("fbId")->equals($fbId)
            ->getQuery()->getSingleResult();

        $biz = $dm->createQueryBuilder("AppBundle:MnemonoBiz")
            ->field("importFrom")->equals("facebookPage")
            ->field("importFromRef")->references($page)
            ->getQuery()->getSingleResult();

        if ($biz == null && $page instanceof FacebookPage){
            $biz = $this->bizBuilder($page, $pageRaw);
            $dm->persist($biz);
            $dm->flush();
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
        if (isset($pageRaw["location"])){
            $location = $this->createLocation($pageRaw["location"]);
            $biz->setLocation($location);
        }
        $biz->setName($pageRaw["name"])
            ->setImportFrom("facebookPage")
            ->setImportFromRef($page)
            ->setLastModDate(new \DateTime());
        return $biz;
    }

    /**
     * @param $rawArray
     * @return Location
     */
    private function createLocation($rawArray){
        $location = new Location();
        $street = (isset($rawArray["street"]) ? $rawArray["street"] : null);
        $city = (isset($rawArray["city"]) ? $rawArray["city"] : null);
        $country = (isset($rawArray["country"]) ? $rawArray["country"] : null);
        $location->setCity($city)
            ->setCountry($country)
            ->setAddress($street);
        $this->getDM()->persist($location);
        return $location;
    }

    /**
     * @return null|DocumentManager
     */
    private function getDM(){
        $dm = $this->getContainer()->get("doctrine_mongodb")->getManager();
        if ($dm instanceof DocumentManager){
            return $dm;
        }else{
            echo "dm is not documentMananger"."\n";
        }
        return null;
    }
}
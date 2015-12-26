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
use AppBundle\Utility\GearmanServiceName;
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
                    $this->createBizByFbId($fbId);
                }
            }else{
                $output->writeln("no fbId");
            }
        }else if ($action == "updateFromFb"){
            $fbIds = $input->getOption('fbId');
            if (!empty($fbIds)){
                foreach ($fbIds as $fbId){
                    $this->updateBizByFbId($fbId);
                }
            }else{
                $output->writeln("no fbId");
            }
        }

    }
    private function updateBizFromFbPageCollection(){
        $this->loopCollectionWithQueryBuilder(
            function ($limit){
                return $this->getFacebookPageRepo()->getQueryBuilder($limit);
            },
            function (FacebookPage $page){
                $this->updateBizByFbId($page->getFbId());
            }
        );
    }
    private function createBizFromFbPageCollection(){
        $this->loopCollectionWithQueryBuilder(
            function ($limit){
                return $this->getFacebookPageRepo()->getQueryBuilder($limit);
            },
            function(FacebookPage $page){
                $this->createBizByFbId($page->getFbId());
            }
        );
    }

    private function createBizByFbId($fbId){
        $json = json_encode(array("fbId" => $fbId));
        $this->getGearman()->doNormalJob(GearmanServiceName::$syncFbPageCreateJob, $json);
    }

    private function updateBizByFbId($fbId){
        $json = json_encode(array("fbId" => $fbId));
        $this->getGearman()->doNormalJob(GearmanServiceName::$syncFbPageUpdateJob, $json);
    }
}
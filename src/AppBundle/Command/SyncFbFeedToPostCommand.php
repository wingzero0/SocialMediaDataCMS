<?php
/**
 * User: kit
 * Date: 15/07/15
 * Time: 21:01
 */

namespace AppBundle\Command;

use AppBundle\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

class SyncFbFeedToPostCommand extends BaseCommand{
    protected function configure(){
        $this->setName("mnemono:sync:fbfeedtopost")
            ->setDescription("sync facebook feed to post")
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
            // TODO implement batch dump
        }else if (!empty($fbIds)){
            foreach ($fbIds as $fbId){
                print_r($fbId);
                if ($action=="createFromFb"){
                    $this->createPostByFeed($fbId);
                }
            }
        }else{
            $output->writeln("no fbId");
        }
    }

    private function createPostByFeed($fbId){;
        $feeds = $this->getDM()->createQueryBuilder("AppBundle:FacebookFeed")
        ->field("fbId")->equals($fbId)->getQuery()->execute();
        foreach($feeds as $feed){
            $likes = $feed->getLikes();
            print_r($likes["summary"]);
        }
    }
}
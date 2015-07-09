<?php
/**
 * Created by PhpStorm.
 * User: kitlei
 * Date: 7/9/2015
 * Time: 9:35 AM
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncFbPageToBizCommand extends ContainerAwareCommand{
    protected function configure(){
        $this->setName("mnemono:sync:fbpagetobiz")
            ->setDescription("sync facebook page to biz")
            ->addOption('fbId', null ,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'the specific fbId you want to sync')
            ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $fbId = $input->getOption('fbId');
        if (!empty($fbId)){
            $output->writeln("your first fbId is ". $fbId[0]);
        }else{
            $output->writeln("no fbId");
        }
    }
}
<?php
/**
 * User: kit
 * Date: 10/28/2015
 * Time: 2:49 PM
 */

namespace AppBundle\Command;

use AppBundle\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PreprocessPostReportCommand extends BaseCommand{
    protected function configure(){
        $this->setName("mnemono:post:review")
            ->setDescription("calculate a post ranking score")
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $output->writeln("finish");
    }
}
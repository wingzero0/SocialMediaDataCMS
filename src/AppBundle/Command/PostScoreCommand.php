<?php
/**
 * User: kit
 * Date: 7/22/2015
 * Time: 9:37 AM
 */

namespace AppBundle\Command;

use AppBundle\Command\BaseCommand;
use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\Facebook\FacebookFeedTimestamp;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\Post;
use AppBundle\Utility\GearmanServiceName;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PostScoreCommand extends BaseCommand{
    protected function configure(){
        $this->setName("mnemono:post:score")
            ->setDescription("calculate a post's score")
            ->addOption('id', null ,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'the id of post which you want to calculate score')
            ->addOption('genTest', null,
                InputOption::VALUE_NONE,
                'generate a test list of command for dummy testing')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $genTestFlag = $input->getOption('genTest');
        if ($genTestFlag){
            $this->genTestList();
            return;
        }
        $ids = $input->getOption('id');
        if (!empty($ids)){
            foreach ($ids as $id){
                $json = json_encode(array("id" => $id));
                $this->getGearman()->doBackgroundJob(GearmanServiceName::$postScoreUpdateJob, $json);
            }
        }else{
            $output->writeln("no id");
        }
    }

    private function genTestList(){
        echo "test\n";
        $posts = $this->getPostRepo()->findAllWithSkipAndLimit();
        $counter = 0;
        foreach($posts as $post){
            if($counter > 100){
                break;
            }
            print_r($post->getId()."\n");
            $json = json_encode(array("id" => $post->getId()));
            $this->getGearman()->doBackgroundJob(GearmanServiceName::$postScoreUpdateJob, $json);
            $counter++;
        }
    }
}
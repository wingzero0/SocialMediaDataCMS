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
use AppBundle\Document\Post;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PostRankingCommand extends BaseCommand{
    protected function configure(){
        $this->setName("mnemono:rank:post")
            ->setDescription("calculate a post ranking score")
            ->addOption('id', null ,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'the id of post which you want to rank')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $ids = $input->getOption('id');
        if (!empty($ids)){
            foreach ($ids as $id){
                $post = $this->getDM()->getRepository($this->postDocumentPath)
                    ->find($id);
                $this->getPostRank($post);
            }
        }else{
            $output->writeln("no id");
        }
    }

    private function getPostRank(Post $post){
        $fbFeed = $post->getImportFromRef();
        if ($fbFeed instanceof FacebookFeed){
            $timestamps = $this->getDM()->getRepository($this->facebookFeedTimestampDocumentPath)
                ->findAllByFeed($fbFeed, 10);
            echo "finding timestamp"."\n";
            foreach($timestamps as $timestamp){
                if ($timestamp instanceof FacebookFeedTimestamp){
                    echo $timestamp->getLikesTotalCount()."\n";
                }
            }
        }
    }
}
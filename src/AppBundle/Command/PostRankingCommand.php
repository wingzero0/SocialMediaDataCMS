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
                echo $this->getPostRank($post)."\n";
            }
        }else{
            $output->writeln("no id");
        }
    }

    private function getPostRank(Post $post){
        $fbFeed = $post->getImportFromRef();
        $score = 0.0;
        if ($fbFeed instanceof FacebookFeed){
            $timestamps = $this->getDM()->getRepository($this->facebookFeedTimestampDocumentPath)
                ->findAllByFeed($fbFeed, 12);
            echo "finding timestamp"."\n";
            $likes = array();
            $comments = array();
            foreach($timestamps as $timestamp){
                if ($timestamp instanceof FacebookFeedTimestamp){
                    $likes[] = $timestamp->getLikesTotalCount();
                    $comments[] = $timestamp->getCommentsTotalCount();
                }
            }
            $tmpLikeScore = 0;
            $tmpCommentScore = 0;
            for ($i = 0;$i < count($likes) - 1; $i++){
                $tmpLikeScore += ($likes[$i] - $likes[$i + 1]);
                $tmpCommentScore += ($comments[$i] - $comments[$i+1]);
            }
            echo $tmpLikeScore."\n";
            echo $tmpCommentScore."\n";
            $score += ($tmpLikeScore * $this->getWeighting("deltaLike")
                + $tmpCommentScore * $this->getWeighting("deltaComment"));
            var_dump($likes);
            var_dump($comments);
            if (!empty($likes)){
                $score += ($likes[0] * $this->getWeighting("totalLikes")
                    + $comments[0] * $this->getWeighting("totalComments"));
            }
        }
        return $score;
    }
    private function getWeighting($key){
        return 1.0;
    }
}
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
                $this->updatePostLocalScore($post);
                $this->updatePostFinalScore($post);
            }
        }else{
            $output->writeln("no id");
        }
    }

    private function updatePostFinalScore(Post $post){
        $localWeight = $this->getWeighting("localWeight");
        $globalWeight = $this->getWeighting("globalWeight");
        $adminWeight = $this->getWeighting("adminWeight");

        $finalScore = $post->updateFinalScore($localWeight , $globalWeight , $adminWeight );
        $this->persistPost($post);
        return $finalScore;
    }

    /**
     * @param Post $post
     * @return float|int
     */
    private function updatePostLocalScore(Post $post){
        $fbFeed = $post->getImportFromRef();
        $score = 0.0;
        if ($fbFeed instanceof FacebookFeed){
            $timestamps = $this->getDM()->getRepository($this->facebookFeedTimestampDocumentPath)
                ->findAllByFeed($fbFeed, 12);
            $likes = array();
            $comments = array();
            foreach($timestamps as $timestamp){
                if ($timestamp instanceof FacebookFeedTimestamp){
                    $likes[] = $timestamp->getLikesTotalCount();
                    $comments[] = $timestamp->getCommentsTotalCount();
                }
            }
            $deltaLikeScore = 0;
            $deltaCommentScore = 0;
            for ($i = 0;$i < count($likes) - 1; $i++){
                $deltaLikeScore += ($likes[$i] - $likes[$i + 1]);
                $deltaCommentScore += ($comments[$i] - $comments[$i+1]);
            }
            $score += ($deltaLikeScore * $this->getWeighting("deltaLikes")
                + $deltaCommentScore * $this->getWeighting("deltaComments"));
            if (!empty($likes)){
                $score += ($likes[0] * $this->getWeighting("totalLikes")
                    + $comments[0] * $this->getWeighting("totalComments"));
            }
        }
        $post->setLocalScore($score);
        $this->persistPost($post);
        return $score;
    }

    /**
     * @param $key
     * @return float
     */
    private function getWeighting($key){
        $weighting = $this->getWeightingRepo()->findOneByName($key);
        if ($weighting == null){
            return 1.0;
        }
        return $weighting->getValue();
    }

    /**
     * @param Post $post
     */
    private function persistPost(Post $post){
        $dm = $this->getDM();
        $dm->persist($post);
        $dm->flush();
    }
}
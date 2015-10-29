<?php
/**
 * User: kit
 * Date: 10/28/2015
 * Time: 2:49 PM
 */

namespace AppBundle\Command;

use AppBundle\Command\BaseCommand;
use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\Post;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\PostForReview;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PreprocessPostReportCommand extends BaseCommand{
    private $endDate;
    private $startDate;
    private $allBiz;
    protected function configure(){
        $this->setName("mnemono:post:review")
            ->setDescription("calculate a post ranking score")
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $output->writeln("finish");
        $this->getAllUpdatedPost();
    }

    private function getAllUpdatedPost(){
        $this->resetDateRange();
        $this->allBiz = array();
        $this->loopCollectionWithSkipParam(function($limit, $skip){
            return $this->getBizQueryBuilder($limit, $skip);
        }, function(MnemonoBiz $biz){
            $bizId = (string) ($biz->getId());
            $this->allBiz[$bizId] = $biz;
        });
        echo count($this->allBiz);
        $tmp = new \MongoDate();
        $batchNo = $tmp->sec;

        foreach ($this->allBiz as $biz){
            $this->process($biz, $batchNo);
        }
    }

    private function process(MnemonoBiz $biz, $batchNo){
        $updatedPosts = array();
        $this->loopCollectionWithSkipParam(function($limit, $skip) use ($biz){
            $this->getDM()->persist($biz);
            return $this->getPostQueryBuilder($biz, $limit, $skip)->sort("finalScore");
        }, function(Post $post) use (&$updatedPosts){
            $updatedPosts[] = $post;
        });
        $i = 1;
        $this->resetDM();
        $dm = $this->getDM();
        foreach($updatedPosts as $post){
            if ($post instanceof Post){
                $postR = new PostForReview();
                $postR->setPost($post);
                $postR->setBatch($batchNo);
                $postR->setRankPosition($i);
                $postR->setTags($post->getTags());
                $dm->merge($postR);
                $dm->flush();
                $i++;
            }
        }
    }

    /**
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    private function getBizQueryBuilder($limit, $skip){
        $postRepo = $this->getMnemenoBizRepo();
        return $postRepo->getQueryBuilderFindAllByDateRange($this->startDate, $this->endDate, $limit, $skip);
    }
    /**
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    private function getPostQueryBuilder(MnemonoBiz $biz, $limit, $skip){
        $postRepo = $this->getPostRepo();
        return $postRepo->getQueryBuilderFindAllByBizAndDateRange($biz, $this->startDate, $this->endDate, $limit, $skip);
    }

    /**
     * @return int
     */
    private function getDateRangeParameter(){
        return 3;
    }
    private function resetDateRange(){
        $dateRangeParameter = $this->getDateRangeParameter();
        $this->endDate = new \DateTime();
        $this->startDate = clone($this->endDate);
        $this->startDate->sub(new \DateInterval('P'.$dateRangeParameter."D"));
    }
}
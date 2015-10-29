<?php
/**
 * User: kit
 * Date: 10/28/2015
 * Time: 2:49 PM
 */

namespace AppBundle\Command;

use AppBundle\Command\BaseCommand;
use AppBundle\Document\Post;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PreprocessPostReportCommand extends BaseCommand{
    private $endDate;
    private $startDate;
    protected function configure(){
        $this->setName("mnemono:post:review")
            ->setDescription("calculate a post ranking score")
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $output->writeln("finish");
    }

    private function getAllUpdatedPost(){
        $this->resetDateRange();
        $this->loopCollectionWithSkipParam(function($limit, $skip){
            return $this->getPostQueryBuilder($limit, $skip);
        }, function(Post $post){
            var_dump($post);
        });
    }

    /**
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    private function getPostQueryBuilder($limit, $skip){
        $postRepo = $this->getPostRepo();
        return $postRepo->getQueryBuilderFindAllFeedByDateRange($this->startDate, $this->endDate, $limit, $skip);
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
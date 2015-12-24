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
use AppBundle\Utility\GearmanServiceName;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PostReviewCommand extends BaseCommand{
    private $endDate;
    private $startDate;
    private $allBiz;
    protected function configure(){
        $this->setName("mnemono:post:review")
            ->setDescription("generate ranking of post in biz for review")
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $json = json_encode(array("id" => null));
        $this->getGearman()->doBackgroundJob(GearmanServiceName::$postReviewRankJob, $json);

        $allBiz = $this->getAllBiz();

        foreach ($allBiz as $biz){
            // TODO should control biz order
            $json = json_encode(array("id" => $biz->getId()));
            $this->getGearman()->doBackgroundJob(GearmanServiceName::$postReviewRankJob, $json);
        }
    }

    private function getAllBiz(){
        $this->allBiz = array();
        $this->loopCollectionWithSkipParam(function($limit, $skip){
            return $this->getBizQueryBuilder($limit, $skip);
        }, function(MnemonoBiz $biz){
            $bizId = (string) ($biz->getId());
            $this->allBiz[$bizId] = $biz;
        });
        echo count($this->allBiz);
        return $this->allBiz;
    }
    /**
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    private function getBizQueryBuilder($limit, $skip){
        $bizRepo = $this->getMnemenoBizRepo();
        return $bizRepo->getQueryBuilderFindAll($limit, $skip);
    }
}
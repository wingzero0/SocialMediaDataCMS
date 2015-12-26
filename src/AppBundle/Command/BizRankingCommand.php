<?php
/**
 * User: kit
 * Date: 10/16/2015
 * Time: 8:25 AM
 */

namespace AppBundle\Command;

use AppBundle\Command\BaseCommand;
use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\Facebook\FacebookPage;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Repository\Facebook\FacebookFeedRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BizRankingCommand extends BaseCommand{
    protected function configure(){
        $this->setName("mnemono:biz:score")
            ->setDescription("calculate a biz ranking score")
            ->addOption('id', null ,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'the id of biz which you want to rank')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $ids = $input->getOption('id');
        if (!empty($ids)){
            foreach ($ids as $id){
                $biz = $this->getMnemenoBizRepo()->findOneBy(array('id' => $id));
                $this->updateBizScore($biz);
            }
        }else{
            $output->writeln("no id");
        }
    }

    private function updateBizScore(MnemonoBiz $biz){
        $page = $biz->getImportFromRef();
        $fromDate = null;
        $toDate = null;
        $feedCount = 0;
        $avgLike = 0;
        $avgComment = 0;
        if ($page instanceof FacebookPage){
            $id = $page->getId();
            $this->loopCollectionWithQueryBuilder(function($limit) use ($id){
                return $this->getQueryBuilder($id, $limit);
            }, function(FacebookFeed $feed)  use (&$feedCount, &$avgLike, &$avgComment){
                $feedCount = $feedCount + 1;
                $avgLike += $feed->getLikesTotalCount();
                $avgComment += $feed->getCommentsTotalCount();
            });
        }
        echo "feedCount:". $feedCount."\n";
        $avgLike /= $feedCount;
        echo "avgLikes:". $avgLike."\n";
        $avgComment /= $feedCount;
        echo "avgComments:".$avgComment."\n";
        $globalScore = $avgLike * $this->getWeighting("avgLikes") + $avgComment * $this->getWeighting("avgComments");
        $biz->setGlobalScore($globalScore);
        $this->getDM()->persist($page);
        $this->getDM()->persist($biz);
        $this->getDM()->flush();
    }

    /**
     * @param string $id
     * @param int $limit
     * @return \Doctrine\MongoDB\Query\Builder
     */
    private function getQueryBuilder($id, $limit){
        $fbPage = $this->getFacebookPageRepo()->findOneById($id);

        list($fromDate, $toDate) = $this->getFromToDate();
        $qb = $this->getFbFeedRepo()
            ->getQueryBuilderByPageAndDateRange($fbPage, $fromDate, $toDate, $limit);
        return $qb;
    }

    /**
     * @return array
     */
    private function getFromToDate(){
        $windowSize = 30;
        $toDate = new \DateTime();
        $fromDate = clone $toDate;
        $fromDate->sub(new \DateInterval('P'.$windowSize.'D'));
        return array($fromDate->format(\DateTime::ISO8601), $toDate->format(\DateTime::ISO8601));
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
}
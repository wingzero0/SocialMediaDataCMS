<?php
/**
 * User: kit
 * Date: 15/07/15
 * Time: 21:01
 */

namespace AppBundle\Command;

use AppBundle\Command\BaseCommand;
use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\Facebook\FacebookPage;
use AppBundle\Document\Facebook\FacebookMeta;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\Post;
use AppBundle\Utility\GearmanServiceName;
use AppBundle\Utility\LoopCollectionStrategy;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use AppBundle\Repository\Facebook\FacebookFeedRepository;

class SyncFbFeedToPostCommand extends BaseCommand{
    protected function configure(){
        $this->setName("mnemono:sync:fbfeedtopost")
            ->setDescription("sync facebook feed to post")
            ->addOption('action', null,
                InputOption::VALUE_OPTIONAL,
                'over write current biz value with facebook page',
                'createFromFbCollection')
            ->addOption('fromDate', null,
                InputOption::VALUE_OPTIONAL,
                'from date parameter, for --action=createFromFbCollection',
                '')
            ->addOption('toDate', null,
                InputOption::VALUE_OPTIONAL,
                'to date parameter, for --action=createFromFbCollection',
                '999999')
            ->addOption('fbId', null ,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'the specific fbId you want to sync')
            ->addOption('removeSource', null,
                InputOption::VALUE_NONE, '');
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $action = $input->getOption('action');
        if ($action == "createFromFbCollection"){
            $fromDateStr = $input->getOption("fromDate");
            $toDateStr = $input->getOption("toDate");
            $this->createPostFromFbFeedCollection($fromDateStr, $toDateStr);
        }else if ($action == "updateFromFbCollection"){
            $fromDateStr = $input->getOption("fromDate");
            $toDateStr = $input->getOption("toDate");
            $this->updatePostFromFbFeedCollection($fromDateStr, $toDateStr);
        }else if ($action == "createFromFb"){
            $fbIds = $input->getOption('fbId');
            if (!empty($fbIds)){
                foreach ($fbIds as $fbId){
                    $this->createPostByFbId($fbId);
                }
            }else{
                $output->writeln("no fbId");
            }
        }else if ($action == "updateFromFb"){
            $fbIds = $input->getOption('fbId');
            if (!empty($fbIds)){
                foreach ($fbIds as $fbId){
                    $this->updatePostByFbId($fbId);
                }
            }else{
                $output->writeln("no fbId");
            }
        }else if ($action == "removePosts"){
            $fromDateStr = $input->getOption("fromDate");
            $fromDate = \DateTime::createFromFormat(\DateTime::ISO8601, $fromDateStr);
            $toDateStr = $input->getOption("toDate");
            $toDate = \DateTime::createFromFormat(\DateTime::ISO8601, $toDateStr);
            $removeSource = $input->getOption("removeSource");
            $this->removePosts($fromDate, $toDate, $removeSource);
        }
    }

    /**
     * @param \DateTime $fromDate
     * @param \DateTime $toDate
     * @param boolean $removeSource
     */
    private function removePosts(\DateTime $fromDate, \DateTime $toDate, $removeSource){
        $loop = new LoopCollectionStrategy();
        $loop->loopCollectionWithQueryBuilder(function($limit) use ($fromDate, $toDate){
            $qb = $this->getPostRepo()->getQueryBuilderFindAllByDate($fromDate, $toDate)->sort(array("id" => 1));
            $qb->limit($limit);
            return $qb;
        }, function (Post $post) use ($removeSource) {
            $this->removePostById($post->getId(), $removeSource);
        }, function (){
            // Do nothing;
        });
    }

    /**
     * @param string $fbId
     */
    private function createPostByFbId($fbId){
        $json = json_encode(array("fbId" => $fbId));
        $this->getGearman()->doBackgroundJob(GearmanServiceName::$syncFbFeedCreateJob, $json);
    }

    /**
     * @param string $fbId
     */
    private function updatePostByFbId($fbId){
        $json = json_encode(array("fbId" => $fbId));
        $this->getGearman()->doBackgroundJob(GearmanServiceName::$syncFbFeedUpdateJob, $json);
    }

    /**
     * @param string $id
     * @param boolean $removeSource
     */
    private function removePostById($id, $removeSource){
        $json = json_encode(array("id" => $id, "removeSource" => $removeSource));
        $this->getGearman()->doBackgroundJob(GearmanServiceName::$syncFbFeedRemoveJob, $json);
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     */
    private function createPostFromFbFeedCollection($fromDate, $toDate){
        $this->loopCollectionWithQueryBuilder(
            function($limit) use ($fromDate, $toDate){
                return $this->getFbFeedRepo()->getQueryBuilderByDateRange($fromDate, $toDate, $limit);
            },
            function(FacebookFeed $feed){
                $this->createPostByFbId($feed->getFbId());
            }
        );
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     */
    private function updatePostFromFbFeedCollection($fromDate, $toDate){
        $this->loopCollectionWithQueryBuilder(
            function($limit) use ($fromDate, $toDate){
                return $this->getFbFeedRepo()->getQueryBuilderByDateRange($fromDate, $toDate, $limit);
            },
            function(FacebookFeed $feed){
                $this->updatePostByFbId($feed->getFbId());
            }
        );
    }
}
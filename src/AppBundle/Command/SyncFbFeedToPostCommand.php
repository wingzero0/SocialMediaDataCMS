<?php
/**
 * User: kit
 * Date: 15/07/15
 * Time: 21:01
 */

namespace AppBundle\Command;

use AppBundle\Command\BaseCommand;
use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\Facebook\FacebookMeta;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\Post;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

class SyncFbFeedToPostCommand extends BaseCommand{
    protected function configure(){
        $this->setName("mnemono:sync:fbfeedtopost")
            ->setDescription("sync facebook feed to post")
            ->addOption('action', null,
                InputOption::VALUE_OPTIONAL,
                'over write current biz value with facebook page',
                'dumpFromFb')
            ->addOption('fbId', null ,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'the specific fbId you want to sync')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $fbIds = $input->getOption('fbId');
        $action = $input->getOption('action');
        if ($action == "dumpFromFb"){
            // TODO implement batch dump
        }else if (!empty($fbIds)){
            foreach ($fbIds as $fbId){
                if ($action=="createFromFb"){
                    $this->createPostByFeed($fbId);
                }
            }
        }else{
            $output->writeln("no fbId");
        }
    }

    private function createPostByFeed($fbId){
        $dm = $this->getDM();
        $feed = $dm->createQueryBuilder($this->facebookFeedDocumentPath)
            ->field("fbId")->equals($fbId)->getQuery()->getSingleResult();
        if ($feed instanceof FacebookFeed){
            $post = $this->postBuilder($feed);
            $dm->persist($post->getMeta());
            $dm->persist($post);
            $dm->flush();
            $dm->clear();
        }
    }

    /**
     * @param FacebookFeed $feed
     * @return Post
     */
    private function postBuilder(FacebookFeed $feed){
        $post = new Post();
        $post->setImportFrom("facebookFeed");
        $post->setImportFromRef($feed);
        $post->setContent($feed->getMessage());
        $post->setPublishStatus("review");
        $meta = $this->fbMetaBuilder($feed);
        $post->setMeta($meta);
        $biz = $this->getDM()->createQueryBuilder($this->mnemonoBizDocumentPath)
            ->field("importFrom")->equals("facebookPage")
            ->field("importFromRef")->references($feed->getFbPage())
            ->getQuery()->getSingleResult();

        if ($biz instanceof MnemonoBiz){
            $post->setMnemonoBiz($biz);
        }
        return $post;
    }
    private function fbMetaBuilder(FacebookFeed $feed){
        $meta = new FacebookMeta();
        $likes = $feed->getLikes();
        $comments = $feed->getComments();
        $meta->setFbId($feed->getFbId());
        $meta->setFbTotalLikes($likes["summary"]["total_count"]);
        $meta->setFbTotalComments($comments["summary"]["total_count"]);
        return $meta;
    }
}
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
            ->addOption('fromDate', null,
                InputOption::VALUE_OPTIONAL,
                'from date parameter, for --action=dumpFromFb',
                '')
            ->addOption('toDate', null,
                InputOption::VALUE_OPTIONAL,
                'to date parameter, for --action=dumpFromFb',
                '999999')
            ->addOption('fbId', null ,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'the specific fbId you want to sync')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $action = $input->getOption('action');
        if ($action == "dumpFromFb"){
            $fromDate = $input->getOption("fromDate");
            $toDate = $input->getOption("toDate");
            $this->createPostFromFbFeedCollection($fromDate, $toDate);
        }else if ($action == "createFromFb"){
            $fbIds = $input->getOption('fbId');
            if (!empty($fbIds)){
                foreach ($fbIds as $fbId){
                    $this->createPostByFeedId($fbId);
                }
            }else{
                $output->writeln("no fbId");
            }
        }
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     */
    private function createPostFromFbFeedCollection($fromDate, $toDate){
        $dm = $this->getDM();
        $feeds = $dm->createQueryBuilder($this->facebookFeedDocumentPath)
            ->field("createdTime")->gte($fromDate)
            ->field("createdTime")->lte($toDate)
            ->getQuery()->execute();

        foreach($feeds as $feed){
            if ($feed instanceof FacebookFeed){
                $post = $this->createPost($feed);
                if ($post != null){$this->persistPost($post);}
            }
        }
    }

    /**
     * @param string $fbId
     */
    private function createPostByFeedId($fbId){
        $dm = $this->getDM();
        $feed = $dm->createQueryBuilder($this->facebookFeedDocumentPath)
            ->field("fbId")->equals($fbId)->getQuery()->getSingleResult();
        if ($feed instanceof FacebookFeed){
            $post = $this->createPost($feed);
            if ($post != null){$this->persistPost($post);}
        }
    }

    private function persistPost(Post $post){
        $dm = $this->getDM();
        $dm->persist($post->getMeta());
        $dm->persist($post);
        $dm->flush();
        $dm->clear();
    }

    /**
     * @param FacebookFeed $feed
     * @return Post
     */
    private function createPost(FacebookFeed $feed){
        $post = $this->getDM()->createQueryBuilder($this->postDocumentPath)
            ->field("importFrom")->equals("facebookFeed")
            ->field("importFromRef")->references($feed)
            ->getQuery()->getSingleResult();
        if ($post != null){
            return null;
        }
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
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
                'createFromFbCollection')
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
        if ($action == "createFromFbCollection"){
            $fromDate = $input->getOption("fromDate");
            $toDate = $input->getOption("toDate");
            $this->createPostFromFbFeedCollection($fromDate, $toDate);
        }else if ($action == "updateFromFbCollection"){
            $fromDate = $input->getOption("fromDate");
            $toDate = $input->getOption("toDate");
            $this->updatePostFromFbFeedCollection($fromDate, $toDate);
        }else if ($action == "createFromFb"){
            $fbIds = $input->getOption('fbId');
            if (!empty($fbIds)){
                foreach ($fbIds as $fbId){
                    $this->createPostByFeedId($fbId);
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
        }
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     */
    private function createPostFromFbFeedCollection($fromDate, $toDate){
        $dm = $this->getDM();
        $limit = 1;
        $lastFeedId = null;
        $firstRun = true;

        $client = new \MongoClient();
        $col = $client->selectCollection("Mnemono", "FacebookFeed");
        do{

            $query = null;
            if (!$firstRun){
                $query = array(
                    "created_time" => array(
                        "\$gte" => $fromDate,
                        "\$lte" => $toDate
                    ),
                    "_id" => array(
                        "\$gt" => new \MongoId($lastFeedId)
                    ),
                );
            }else{
                $query = array(
                    "created_time" => array(
                        "\$gte" => $fromDate,
                        "\$lte" => $toDate
                    ),
                );
            }
            $feeds = $col->find($query)->limit(1);
/*
            $qb = $dm->createQueryBuilder($this->facebookFeedDocumentPath)
                ->field("createdTime")->gte($fromDate)
                ->field("createdTime")->lte($toDate)
                ->hydrate(false)
                ->limit($limit)->sort("id")

                ;

            if (!$firstRun){
               $qb->field("id")->gt($lastFeedId);
            }
            $feeds = $qb->getQuery()->execute();
*/
            $newFeedCount = $feeds->count(true);
            print_r($newFeedCount);
            foreach($feeds as $feed){
                if ($feed instanceof FacebookFeed){
                    //$post = $this->createPost($feed);
                    //if ($post != null){$this->persistPost($post);}
                    $lastFeedId = $feed->getId();
                    $post = null;
                }else{
                    $lastFeedId = (string) $feed["_id"];
                }
                $feed = null;
            }
            $firstRun = false;
            $dm->clear();
        }while($newFeedCount > 0);
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     */
    private function updatePostFromFbFeedCollection($fromDate, $toDate){
        $dm = $this->getDM();
        $feeds = $dm->createQueryBuilder($this->facebookFeedDocumentPath)
            ->field("createdTime")->gte($fromDate)
            ->field("createdTime")->lte($toDate)
            ->getQuery()->execute();

        foreach($feeds as $feed){
            if ($feed instanceof FacebookFeed){
                $post = $this->queryPostByFeed($feed);
                $this->updatePostByRef($post);
            }
        }
    }

    /**
     * @param string $fbId
     */
    private function updatePostByFbId($fbId){
        $feed = $this->queryFeedByFbId($fbId);
        $post = $this->queryPostByFeed($feed);
        $this->updatePostByRef($post);
    }


    /**
     * @param Post $post
     */
    private function updatePostByRef(Post $post){
        $ref = $post->getImportFromRef();
        if ($ref instanceof FacebookFeed){
            $post->setContent($ref->getMessage());
            $post->setMeta($this->fbMetaBuilder($ref));
            $this->persistPost($post);
        }
    }

    /**
     * @param $fbId
     * @return FacebookFeed|null
     */
    private function queryFeedByFbId($fbId){
        $feed = $this->getDM()->createQueryBuilder($this->facebookFeedDocumentPath)
            ->field("fbId")->equals($fbId)->getQuery()->getSingleResult();
        return $feed;
    }

    /**
     * @param FacebookFeed $feed
     * @return Post|null
     */
    private function queryPostByFeed(FacebookFeed $feed){
        $post = $this->getDM()->getRepository($this->postDocumentPath)
            ->findOneByFeed($feed);
        return $post;
    }

    /**
     * @param string $fbId
     */
    private function createPostByFeedId($fbId){
        $feed = $this->queryFeedByFbId($fbId);
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
        $post = $this->queryPostByFeed($feed);
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
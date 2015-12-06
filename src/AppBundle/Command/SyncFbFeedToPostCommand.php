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
        $this->loopCollectionWithQueryBuilder(
            function($limit) use ($fromDate, $toDate){
                return $this->getFbFeedRepo()->getQueryBuilderByDateRange($fromDate, $toDate, $limit);
            },
            function(FacebookFeed $feed){
                $post = $this->createPost($feed);
                if ($post != null){$this->persistPost($post);}
            }
        );
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     */
    private function updatePostFromFbFeedCollection($fromDate, $toDate){
        $this->loopFbFeedCollection($fromDate, $toDate,
            function(FacebookFeed $feed) {
                $post = $this->queryPostByFeed($feed);
                if ($post instanceof Post) {
                    $this->updatePostByRef($post);
                }
            }
        );
    }

    private function loopFbFeedCollection($fromDate, $toDate, $callBack){
        $limit = 100;
        $lastFeedId = null;
        $firstRun = true;

        do{
            $this->resetDM();
            $qb = $this->getFbFeedRepo()->getQueryBuilderByDateRange($fromDate, $toDate, $limit);

            if (!$firstRun){
                $qb->field("id")->gt($lastFeedId);
            }
            $feeds = $qb->getQuery()->execute();

            $newFeedCount = $feeds->count(true);
            foreach($feeds as $feed){
                if ($feed instanceof FacebookFeed){
                    $callBack($feed);
                    $lastFeedId = $feed->getId();
                }else{
                    $newFeedCount = -1; //something go wrong;
                }
            }
            $firstRun = false;
        }while($newFeedCount > 0);
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
            $post->setOriginalLink($ref->getShortLink());
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
        $json = json_encode(array("fbId" => $fbId));
        $this->getContainer()->get('gearman')->doBackgroundJob('AppBundleServicesSyncFbFeedService~createPost', $json);
    }

    private function persistPost(Post $post){
        $dm = $this->getDM();
        $timing = new \DateTime();
        if (!$post->getId()){
            $post->setCreateAt($timing);
        }
        $post->setUpdateAt($timing);
        $biz = $post->getMnemonoBiz();
        if (!$biz instanceof MnemonoBiz) {
            var_dump($post->getImportFromRef()->getId());
        }
        $biz->setLastPostUpdateAt($timing);
        $dm->persist($biz);
        $dm->persist($post);
        $dm->flush();
        $dm->clear();
    }

    /**
     * @param FacebookFeed $feed
     * @return Post|null
     */
    private function createPost(FacebookFeed $feed){
        $fbPage = $feed->getFbPage();
        if ($fbPage->getExcpetion() == true){
            return null;
        }

        $post = $this->queryPostByFeed($feed);
        if ($post != null){
            return null;
        }
        $post = new Post();
        $post->setImportFrom("facebookFeed");
        $post->setImportFromRef($feed);
        $post->setContent($feed->getMessage());
        $post->setPublishStatus("review");
        $post->setOriginalLink($feed->getShortLink());
        $meta = $this->fbMetaBuilder($feed);
        $post->setMeta($meta);
        $biz = $this->getDM()->createQueryBuilder($this->mnemonoBizDocumentPath)
            ->field("importFrom")->equals("facebookPage")
            ->field("importFromRef")->references($feed->getFbPage())
            ->getQuery()->getSingleResult();

        if ($biz instanceof MnemonoBiz){
            $post->setMnemonoBiz($biz);
            $post->addTag($biz->getCategory());
        }
        return $post;
    }
    private function fbMetaBuilder(FacebookFeed $feed){
        $meta = new FacebookMeta();
        $likes = $feed->getLikes();
        $comments = $feed->getComments();
        $meta->setFbId($feed->getFbId());

        $likeCount = 0;
        if (isset($likes["summary"]) && isset($likes["summary"]["total_count"])){
            $likeCount = $likes["summary"]["total_count"];
        }
        $meta->setFbTotalLikes($likeCount);

        $commentCount = 0;
        if (isset($comments["summary"]) && isset($comments["summary"]["total_count"])){
            $commentCount = $comments["summary"]["total_count"];
        }
        $meta->setFbTotalComments($commentCount);
        return $meta;
    }
}
<?php
/**
 * User: kit
 * Date: 15/07/15
 * Time: 21:04
 */

namespace AppBundle\Command;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\MongoDB\Query\Builder;
use AppBundle\Repository\Facebook\FacebookPageRepository;
use AppBundle\Repository\Facebook\FacebookFeedRepository;
use AppBundle\Repository\Settings\WeightingRepository;

abstract class BaseCommand extends ContainerAwareCommand{
    protected $facebookPageDocumentPath = "AppBundle:Facebook\\FacebookPage";
    protected $facebookFeedDocumentPath = "AppBundle:Facebook\\FacebookFeed";
    protected $facebookFeedTimestampDocumentPath = "AppBundle:Facebook\\FacebookFeedTimestamp";
    protected $weightingDocumentPath = "AppBundle:Settings\\Weighting";
    protected $mnemonoBizDocumentPath = "AppBundle:MnemonoBiz";
    protected $postDocumentPath = "AppBundle:Post";
    private $documentManager = null;
    /**
     * @param bool $reset
     * @return null|DocumentManager
     */
    protected function getDM($reset = false){
        if ($this->documentManager == null){
            $this->documentManager = $this->getContainer()->get("doctrine_mongodb")->getManager();
        }
        if ($reset == true && $this->documentManager instanceof DocumentManager){
            $this->documentManager = DocumentManager::create(new Connection(), $this->documentManager->getConfiguration());
        }
        return $this->documentManager;
    }
    protected function resetDM(){
        $this->getDM(true);
    }

    /**
     * @param $queryBuilderCallback
     * @param $reducerCallBack
     *
     * it will reset the dm in the loop
     */
    protected function loopCollectionWithQueryBuilder($queryBuilderCallback, $reducerCallBack){
        $limit = 100;
        $lastFeedId = null;
        $firstRun = true;

        do{
            $this->resetDM();
            $qb = $queryBuilderCallback($limit);

            if (!$qb instanceof Builder){
                break;
            }

            if (!$firstRun){
                $qb->field("id")->gt($lastFeedId);
            }
            $cursor = $qb->getQuery()->execute();

            $recordCount = $cursor->count(true);
            foreach($cursor as $record){
                $reducerCallBack($record);
                $lastFeedId = $record->getId();
            }
            $firstRun = false;
        }while($recordCount > 0);
    }

    /**
     * @return FacebookPageRepository
     */
    protected function getFacebookPageRepo(){
        return $this->getDM()->getRepository($this->facebookPageDocumentPath);
    }
    /**
     * @return FacebookFeedRepository
     */
    protected function getFbFeedRepo(){
        return $this->getDM()->getRepository($this->facebookFeedDocumentPath);
    }
    /**
     * @return WeightingRepository
     */
    protected function getWeightingRepo(){
        return $this->getDM()->getRepository($this->weightingDocumentPath);
    }
}
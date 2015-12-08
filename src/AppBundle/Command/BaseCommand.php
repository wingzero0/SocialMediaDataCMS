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
use AppBundle\Repository\Facebook\FacebookPageRepository;
use AppBundle\Repository\Facebook\FacebookFeedRepository;
use AppBundle\Repository\Settings\WeightingRepository;
use AppBundle\Repository\PostRepository;
use AppBundle\Repository\MnemonoBizRepository;
use AppBundle\Utility\LoopCollectionStrategy;
use AppBundle\Utility\DocumentPath;

abstract class BaseCommand extends ContainerAwareCommand{
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
        $loopS = new LoopCollectionStrategy();
        $loopS->loopCollectionWithQueryBuilder($queryBuilderCallback, $reducerCallBack, function(){
            $this->resetDM();
        });
    }

    protected function loopCollectionWithSkipParam($queryBuilderCallback, $reducerCallBack){
        $loopS = new LoopCollectionStrategy();
        $loopS->loopCollectionWithSkipParam($queryBuilderCallback, $reducerCallBack, function(){
            $this->resetDM();
        });
    }

    /**
     * @return FacebookPageRepository
     */
    protected function getFacebookPageRepo(){
        return $this->getDM()->getRepository(DocumentPath::$facebookPageDocumentPath);
    }
    /**
     * @return FacebookFeedRepository
     */
    protected function getFbFeedRepo(){
        return $this->getDM()->getRepository(DocumentPath::$facebookFeedDocumentPath);
    }
    /**
     * @return WeightingRepository
     */
    protected function getWeightingRepo(){
        return $this->getDM()->getRepository(DocumentPath::$weightingDocumentPath);
    }
    /**
     * @return PostRepository
     */
    protected function getPostRepo(){
        return $this->getDM()->getRepository(DocumentPath::$postDocumentPath);
    }
    /**
     * @return MnemonoBizRepository
     */
    protected function getMnemenoBizRepo(){
        return $this->getDM()->getRepository(DocumentPath::$mnemonoBizDocumentPath);
    }
}
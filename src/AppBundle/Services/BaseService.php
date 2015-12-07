<?php
/**
 * User: kit
 * Date: 05/12/15
 * Time: 9:05 PM
 */

namespace AppBundle\Services;

use AppBundle\Utility\LoopCollectionStrategy;
use AppBundle\Utility\DocumentPath;
use AppBundle\Repository\Facebook\FacebookPageRepository;
use AppBundle\Repository\Facebook\FacebookFeedRepository;
use AppBundle\Repository\Facebook\FacebookFeedTimestampRepository;
use AppBundle\Repository\Settings\WeightingRepository;
use AppBundle\Repository\PostRepository;
use AppBundle\Repository\MnemonoBizRepository;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;

abstract class BaseService {
    private $container;
    private $documentManager = null;
    private $loopCollectionStrategy;

    public function __construct(Container $container){
        $this->setContainer($container);
        $this->loopCollectionStrategy = new LoopCollectionStrategy();
    }

    /**
     * @return LoopCollectionStrategy
     */
    public function getLoopCollectionStrategy()
    {
        return $this->loopCollectionStrategy;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

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
        $this->getLoopCollectionStrategy()
            ->loopCollectionWithQueryBuilder($queryBuilderCallback, $reducerCallBack, function (){
                $this->resetDM();
            });
    }

    protected function loopCollectionWithSkipParam($queryBuilderCallback, $reducerCallBack){
        $this->getLoopCollectionStrategy()
            ->loopCollectionWithSkipParam($queryBuilderCallback, $reducerCallBack, function(){
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
     * @return FacebookFeedTimestampRepository
     */
    protected function getFbFeedTimestampRepo(){
        return $this->getDM()->getRepository(DocumentPath::$facebookFeedTimestampDocumentPath);
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
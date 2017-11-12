<?php
/**
 * User: kit
 * Date: 05/12/15
 * Time: 9:05 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services;

use AppBundle\Utility\LoopCollectionStrategy;
use AppBundle\Utility\DocumentPath;
use AppBundle\Repository\Facebook\FacebookPageRepository;
use AppBundle\Repository\Weibo\WeiboPageRepository;
use AppBundle\Repository\Facebook\FacebookFeedRepository;
use AppBundle\Repository\Weibo\WeiboFeedRepository;
use AppBundle\Repository\Facebook\FacebookFeedTimestampRepository;
use AppBundle\Repository\Settings\WeightingRepository;
use AppBundle\Repository\PostRepository;
use AppBundle\Repository\MnemonoBizRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\DeviceInfoRepository;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use Psr\Log\LoggerInterface;

abstract class BaseService
{
    private $container;
    private $documentManager = null;
    private $loopCollectionStrategy;

    public function __construct(Container $container)
    {
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
     * @return GearmanClient
     */
    protected function getGearman()
    {
        return $this->getContainer()->get('gearman');
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->getContainer()->get('logger');
    }

    /**
     * @param $msg
     */
    protected function logError($msg)
    {
        $dateObj = new \DateTime();
        echo $dateObj->format(\DateTime::ISO8601) . ":" . $msg . "\n";
        $this->getLogger()->error($msg);
    }

    protected function logExecption(\Exception $e)
    {
        $this->logError($e->getMessage());
        $this->logError($e->getTraceAsString());
    }

    /**
     * @param bool $reset
     * @return null|DocumentManager
     */
    protected function getDM($reset = false)
    {
        if ($this->documentManager == null)
        {
            $this->documentManager = $this->getContainer()
                ->get("doctrine_mongodb")
                ->getManager();
        }
        if ($reset == true && $this->documentManager instanceof DocumentManager)
        {
            $this->documentManager = DocumentManager::create(
                new Connection(),
                $this->documentManager->getConfiguration()
            );
        }
        return $this->documentManager;
    }

    protected function resetDM()
    {
        $this->getDM(true);
    }

    /**
     * @return FacebookPageRepository
     */
    protected function getFacebookPageRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$facebookPageDocumentPath);
    }
    /**
     * @return WeiboPageRepository
     */
    protected function getWeiboPageRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$weiboPageDocumentPath);
    }
    /**
     * @return FacebookFeedRepository
     */
    protected function getFbFeedRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$facebookFeedDocumentPath);
    }

    /**
     * @return WeiboFeedRepository
     */
    protected function getWeiboFeedRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$weiboFeedDocumentPath);
    }

    /**
     * @return FacebookFeedTimestampRepository
     */
    protected function getFbFeedTimestampRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$facebookFeedTimestampDocumentPath);
    }
    /**
     * @return WeightingRepository
     */
    protected function getWeightingRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$weightingDocumentPath);
    }
    /**
     * @return PostRepository
     */
    protected function getPostRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$postDocumentPath);
    }
    /**
     * @return MnemonoBizRepository
     */
    protected function getMnemenoBizRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$mnemonoBizDocumentPath);
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$userDocumentPath);
    }

    /**
     * @return DeviceInfoRepository
     */
    protected function getDeviceInfoRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$deviceInfoDocumentPath);
    }
}

<?php
/**
 * User: kit
 * Date: 12/9/2015
 * Time: 1:52 PM
 */

namespace AppBundle\Controller;

use AppBundle\Repository\DeviceInfoRepository;
use AppBundle\Repository\Utility\LogRecordRepository;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ODM\MongoDB\DocumentManager;
use AppBundle\Utility\DocumentPath;
use AppBundle\Repository\PostRepository;
use AppBundle\Repository\Settings\WeightingRepository;
use AppBundle\Repository\Facebook\FacebookFeedRepository;
use AppBundle\Repository\Facebook\FacebookFeedTimestampRepository;
use AppBundle\Repository\Facebook\FacebookPageTimestampRepository;
use AppBundle\Repository\ManagedTagRepository;
use AppBundle\Repository\SpotlightAdsRepository;
use AppBundle\Repository\MnemonoBizRepository;
use Knp\Component\Pager\Paginator;
use Psr\Log\LoggerInterface;
use JMS\Serializer\SerializerInterface;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use AppBundle\Repository\TrendingPostRepository;
use AppBundle\Repository\PopularPostRepository;
use AppBundle\Repository\PendingGamePostRepository;
use AppBundle\Repository\PendingGamePostStatsRepo;
use AppBundle\Repository\BizStatsRepo;
use AppBundle\Repository\BizPostCounStatsRepo;
use AppBundle\Repository\BizPostMetricStatsRepo;
use AppBundle\Repository\PostStatsRepo;
use AppBundle\Repository\PostOverallStatsRepo;
use Doctrine\ODM\MongoDB\Query\Builder;

abstract class AppBaseController extends Controller
{
    /**
     * @return DocumentManager
     */
    protected function getDM()
    {
        return $this->get('doctrine_mongodb')->getManager();
    }
    /**
     * @return Paginator
     */
    protected function getKnpPaginator()
    {
        return $this->get('knp_paginator');
    }
    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->get('logger');
    }

    /**
     * @return SerializerInterface
     */
    protected function getJMSSerializer()
    {
        return $this->get('jms_serializer');
    }
    /**
     * @return GearmanClient
     */
    protected function getGearman()
    {
        return $this->get('gearman');
    }

    /**
     * @param Request $request
     * @return double
     */
    protected function getVersionNum(Request $request)
    {
        $version = $request->attributes->get('version');
        $versionNum = doubleval($version);

        return $versionNum;
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
     * @return FacebookFeedRepository
     */
    protected function getFacebookFeedRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$facebookFeedDocumentPath);
    }

    /**
     * @return FacebookFeedTimestampRepository
     */
    protected function getFacebookFeedTimestampRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$facebookFeedTimestampDocumentPath);
    }

    /**
     * @return FacebookPageTimestampRepository
     */
    protected function getFacebookPageTimestampRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$facebookPageTimestampDocumentPath);
    }

    /**
     * @param string $key the weighting key name
     * @return float
     */
    protected function getWeighting($key)
    {
        $weighting = $this->getWeightingRepo()->findOneByName($key);
        if ($weighting == null)
        {
            return 1.0;
        }
        return $weighting->getValue();
    }

    /**
     * @return ManagedTagRepository
     */
    protected function getManagedTagRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$managedTagDocumentPath);
    }

    /**
     * @return SpotlightAdsRepository
     */
    protected function getSpotlightAdsRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$spotlightAdsDocumentPath);
    }

    /**
     * @return LogRecordRepository
     */
    protected function getLogRecordRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$logRecordDocumentPath);
    }

    /**
     * @return DeviceInfoRepository
     */
    protected function getDeviceInfoRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$deviceInfoDocumentPath);
    }

    /**
     * @return MnemonoBizRepository
     */
    protected function getMnemenoBizRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$mnemonoBizDocumentPath);
    }

    /**
     * @return TrendingPostRepository
     */
    protected function getTrendingPostRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$trendingPostDocumentPath);
    }

    /**
     * @return PopularPostRepository
     */
    protected function getPopularPostRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$popularPostDocumentPath);
    }

    /**
     * @return PendingGamePostRepository
     */
    protected function getPendingGamePostRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$pendingGamePostDocumentPath);
    }

    /**
     * @return PendingGamePostStatsRepo
     */
    protected function getPendingGamePostStatsRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$pendingGamePostStatsDocumentPath);
    }

    /**
     * @return BizStatsRepo
     */
    protected function getBizStatsRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$bizStatsDocumentPath);
    }

    /**
     * @return BizPostCountStatsRepo
     */
    protected function getBizPostCountStatsRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$bizPostCountStatsDocumentPath);
    }

    /**
     * @return BizPostMetricStatsRepo
     */
    protected function getBizPostMetricStatsRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$bizPostMetricStatsDocumentPath);
    }

    /**
     * @return PostStatsRepo
     */
    protected function getPostStatsRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$postStatsDocumentPath);
    }

    /**
     * @return PostOverallStatsRepo
     */
    protected function getPostOverallStatsRepo()
    {
        return $this->getDM()->getRepository(DocumentPath::$postOverallStatsDocumentPath);
    }

    /**
     * @param Request $request
     * @param array|object|string $data
     * @param string|null $groupName
     * @return string
     */
    protected function serialize(Request $request, $data, $groupName = null)
    {
        if ($groupName)
        {
            // TODO enhance control flag for group and version. are they always appeared together?
            $serialize = $this->getJMSSerializer()->serialize(
                array('data' => $data),
                'json',
                SerializationContext::create()->setGroups(array($groupName))
                    ->setVersion($this->getVersionNum($request))
            );
        }
        else
        {
            $serialize = $this->getJMSSerializer()->serialize(
                array('data' => $data),
                'json'
            );
        }

        return $serialize;
    }

    /**
     * @param string[] $inputArray
     * @param Builder $qb
     * @param string $fieldName
     * @return Builder
     */
    protected function compileArrayInFilter($inputArray, Builder $qb, $fieldName)
    {
        $trimmedValueArray = [];
        if (!empty($inputArray) && is_array($inputArray))
        {
            foreach ($inputArray as $value)
            {
                $trimmedValue = trim($value);
                if (!empty($trimmedValue))
                {
                    $trimmedValueArray[] = $trimmedValue;
                }
            }
            if (!empty($trimmedValueArray))
            {
                $qb->field($fieldName)->in($trimmedValueArray);
            }
        }
        return $qb;
    }
}

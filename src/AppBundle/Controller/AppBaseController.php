<?php
/**
 * User: kit
 * Date: 12/9/2015
 * Time: 1:52 PM
 */

namespace AppBundle\Controller;

use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ODM\MongoDB\DocumentManager;
use AppBundle\Utility\DocumentPath;
use AppBundle\Repository\PostRepository;
use AppBundle\Repository\Settings\WeightingRepository;
use AppBundle\Repository\Facebook\FacebookFeedRepository;
use AppBundle\Repository\ManagedTagRepository;
use AppBundle\Repository\SpotlightAdsRepository;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use JMS\Serializer\SerializerInterface;

abstract class AppBaseController extends Controller{
    /**
     * @return DocumentManager
     */
    protected function getDM(){
        return $this->get('doctrine_mongodb')->getManager();
    }
    /**
     * @return Paginator
     */
    protected function getKnpPaginator(){
        return $this->get('knp_paginator');
    }
    /**
     * @return LoggerInterface
     */
    protected function getLogger(){
        return $this->get('logger');
    }

    /**
     * @return SerializerInterface
     */
    protected function getJMSSerializer(){
        return $this->get('jms_serializer');
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
     * @return FacebookFeedRepository
     */
    protected function getFacebookFeedRepo(){
        return $this->getDM()->getRepository(DocumentPath::$facebookFeedDocumentPath);
    }

    /**
     * @param string $key the weighting key name
     * @return float
     */
    protected function getWeighting($key){
        $weighting = $this->getWeightingRepo()->findOneByName($key);
        if ($weighting == null){
            return 1.0;
        }
        return $weighting->getValue();
    }

    /**
     * @return ManagedTagRepository
     */
    protected function getManagedTagRepo(){
        return $this->getDM()->getRepository(DocumentPath::$managedTagDocumentPath);
    }

    /**
     * @return SpotlightAdsRepository
     */
    protected function getSpotlightAdsRepo(){
        return $this->getDM()->getRepository(DocumentPath::$spotlightAdsDocumentPath);
    }

    /**
     * @param array $data
     * @param string|null $groupName
     * @return string
     */
    protected function serialize($data, $groupName = null){
        if ($groupName){
            $serialize = $this->getJMSSerializer()->serialize(
                array('data' => $data),
                'json',
                SerializationContext::create()->setGroups(array($groupName))
            );
        }else{
            $serialize = $this->getJMSSerializer()->serialize(
                array('data' => $data),
                'json'
            );
        }

        return $serialize;
    }
}
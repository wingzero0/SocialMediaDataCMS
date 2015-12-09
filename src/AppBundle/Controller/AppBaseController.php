<?php
/**
 * User: kit
 * Date: 12/9/2015
 * Time: 1:52 PM
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ODM\MongoDB\DocumentManager;
use AppBundle\Utility\DocumentPath;
use AppBundle\Repository\PostRepository;
use AppBundle\Repository\Settings\WeightingRepository;
use AppBundle\Repository\Facebook\FacebookFeedRepository;
use Knp\Component\Pager\Paginator;

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
}
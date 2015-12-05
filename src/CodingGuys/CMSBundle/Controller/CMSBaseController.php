<?php
/**
 * User: kit
 * Date: 04/07/15
 * Time: 16:10
 */

namespace CodingGuys\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ODM\MongoDB\DocumentManager;
use Knp\Component\Pager\Paginator;
use AppBundle\Repository\Settings\WeightingRepository;
use AppBundle\Repository\PostRepository;
use AppBundle\Repository\Facebook\FacebookFeedRepository;
use AppBundle\Utility\DocumentPath;

class CMSBaseController extends Controller{

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

    protected function getPaginator(){
        return $this->get('knp_paginator');
    }
}
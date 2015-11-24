<?php
/**
 * Created by PhpStorm.
 * User: kitlei
 * Date: 10/13/2015
 * Time: 8:25 AM
 */

namespace AppBundle\Repository\Facebook;

use Doctrine\ODM\MongoDB\DocumentRepository;
use AppBundle\Document\Facebook\FacebookPage;

class FacebookPageRepository extends DocumentRepository{
    /**
     * @param int $limit
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getQueryBuilder($limit = 100){
        $qb = $this->createQueryBuilder()
            ->field("exception")->notEqual(true)
            ->limit($limit)->sort("id");
        return $qb;
    }

    /**
     * @return FacebookPage|null
     */
    public function findOneById($id){
        $qb = $this->getQueryBuilder(1)
            ->field("id")->equals($id);
        $cursor = $qb->getQuery()->execute();
        foreach($cursor as $fbPage){
            return $fbPage;
        }
        return null;
    }

    /**
     * @param string $fbId
     * @return FacebookPage|null
     */
    public function findOneByFbId($fbId){
        $qb = $this->getQueryBuilder()
            ->field("fbId")->equals($fbId)
            ->field("exception")->notEqual(true);
        return $qb->getQuery()->getSingleResult();
    }
}
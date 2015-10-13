<?php
/**
 * Created by PhpStorm.
 * User: kitlei
 * Date: 10/13/2015
 * Time: 8:25 AM
 */

namespace AppBundle\Repository\Facebook;

use Doctrine\ODM\MongoDB\DocumentRepository;

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

}
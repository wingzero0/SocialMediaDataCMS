<?php
/**
 * Created by PhpStorm.
 * User: kitlei
 * Date: 8/1/2016
 * Time: 13:48
 */

namespace AppBundle\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;

class ManagedTagRepository extends DocumentRepository{
    /**
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getFindAllQueryBuilder(){
        $qb = $this->createQueryBuilder();
        return $qb;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: kitlei
 * Date: 8/1/2016
 * Time: 13:48
 */

namespace AppBundle\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

class ManagedTagRepository extends DocumentRepository{
    /**
     * @return Builder
     */
    public function getFindAllQueryBuilder(){
        $qb = $this->createQueryBuilder()
            ->sort(array('displaySeq' => 1));
        return $qb;
    }
}

<?php
/**
 * User: kit
 * Date: 14/01/16
 * Time: 10:16 PM
 */

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

class SpotlightAdsRepository extends DocumentRepository{
    /**
     * @return Builder
     */
    public function getFindAllQueryBuilder(){
        $qb = $this->createQueryBuilder()->sort(array("displaySeq" => 1));
        return $qb;
    }
}
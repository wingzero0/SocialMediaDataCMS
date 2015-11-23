<?php
/**
 * User: kit
 * Date: 10/29/2015
 * Time: 1:59 PM
 */

namespace AppBundle\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;
use AppBundle\Document\Facebook\FacebookPage;

class MnemonoBizRepository extends DocumentRepository{
    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getQueryBuilderFindAllByDateRange(\DateTime $startDate, \DateTime $endDate, $limit = 100, $skip = 0){
        $qb = $this->createQueryBuilder()
            ->field("lastPostUpdateAt")->gte($startDate)
            ->field("lastPostUpdateAt")->lte($endDate)
            ->skip($skip)->limit($limit);
        return $qb;
    }

    public function findOneByFbPage(FacebookPage $page){

    }
}
<?php

namespace AppBundle\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;

class BizPostCountStatsRepo extends DocumentRepository
{
    public function findAllByTimeRange(\MongoId $bizId,
                                       \DateTime $start,
                                       \DateTime $end)
    {
        $startKey = $start->format('Y-m-d');
        $endKey = $end->format('Y-m-d');
        return $this->createQueryBuilder()
            ->field('id.biz_id')->equals($bizId)
            ->field('id.date')->gte($startKey)
            ->field('id.date')->lte($endKey)
            ->sort('id.date', 'desc')
            ->getQuery()
            ->execute();
    }
}

<?php
/**
 * User: kit
 * Date: 10/29/2015
 * Time: 9:26 AM
 */

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class PostForReviewRepository extends DocumentRepository{

    /**
     * @param int $batch
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getQueryBuilderFindByBatch($batch){
        return $this->createQueryBuilder()
            ->field("batch")->equals($batch);
    }
}
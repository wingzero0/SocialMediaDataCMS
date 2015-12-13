<?php

namespace AppBundle\Repository;

use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\MnemonoBiz;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

class PostRepository extends DocumentRepository
{
    /**
     * @param FacebookFeed $feed
     * @return AppBundle\Document\Post|null
     */
    public function findOneByFeed(FacebookFeed $feed){
        $post = $this->createQueryBuilder()
            ->field("importFrom")->equals("facebookFeed")
            ->field("importFromRef")->references($feed)
            ->getQuery()->getSingleResult();

        return $post;
    }

    /**
     * @param MnemonoBiz $biz
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getQueryBuilderFindAllByBizAndDateRange(MnemonoBiz $biz, \DateTime $startDate, \DateTime $endDate, $limit = 100, $skip = 0){
        $qb = $this->createQueryBuilder()
            ->field("mnemonoBiz")->references($biz)
            ->field("updateAt")->gte($startDate)
            ->field("updateAt")->lte($endDate)
            ->skip($skip)->limit($limit);
        return $qb;
    }

    /**
     * @param MnemonoBiz $biz
     * @param \DateTime $expireDate
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getQueryBuilderFindNonExpire(MnemonoBiz $biz, \DateTime $expireDate, $limit = 100, $skip = 0){
        $qb = $this->createQueryBuilder()
            ->field("mnemonoBiz")->references($biz)
            ->field("expireDate")->lte($expireDate)
            ->field("softDelete")->notEqual(true)
            ->skip($skip)->limit($limit);
        return $qb;
    }

    /**
     * @param int $skip
     * @param int $limit
     * @return array|null
     */
    public function findAllWithSkipAndLimit($skip = 0, $limit = 100){
        $qb = $this->createQueryBuilder()
            ->skip($skip)->limit($limit)->sort(array("_id" => -1));
        return $qb->getQuery()->execute();
    }

    /**
     * @return Builder
     */
    public function getQueryBuilderSortWithRank(){
        $qb = $this->createQueryBuilder()
            ->field("rankPosition")->exists(true)
            ->field("softDelete")->notEqual(true)
            ->sort(array("rankPosition" => "asc", "finalScore" => "desc"))
        ;
        return $qb;
    }

    public function getPublicQueryBuilderSortWithRank(){
        $qb = $this->getQueryBuilderSortWithRank()
            ->field("publishStatus")->equals("published");
        return $qb;
    }
}
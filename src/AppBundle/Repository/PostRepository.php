<?php

namespace AppBundle\Repository;

use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\Post;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

class PostRepository extends DocumentRepository
{
    /**
     * @param FacebookFeed $feed
     * @return Post|null
     */
    public function findOneByFeed(FacebookFeed $feed){
        $post = $this->createQueryBuilder()
            ->field("importFrom")->equals("facebookFeed")
            ->field("importFromRef")->references($feed)
            ->getQuery()->getSingleResult();

        return $post;
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
     * @param \DateTime $expireDate
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getQueryBuilderFindNonExpire(\DateTime $expireDate = null, $limit = 100, $skip = 0){
        if ($expireDate == null){
            $expireDate = new \DateTime();
        }
        $qb = $this->createQueryBuilder()
            ->field("expireDate")->gte($expireDate)
            ->field("softDelete")->notEqual(true)
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
    public function getQueryBuilderFindNonExpireByBiz(MnemonoBiz $biz, \DateTime $expireDate = null, $limit = 100, $skip = 0){
        $qb = $this->getQueryBuilderFindNonExpire($expireDate, $limit, $skip)
            ->field("mnemonoBiz")->references($biz);
        return $qb;
    }

    /**
     * @param \DateTime $expireDate
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getQueryBuilderFindNonExpireWithBizNotExisit(\DateTime $expireDate = null, $limit = 100, $skip = 0){
        $qb = $this->getQueryBuilderFindNonExpire($expireDate, $limit, $skip);
        $qb->addOr($qb->expr()->field("mnemonoBiz")->exists(false))
            ->addOr($qb->expr()->field("mnemonoBiz")->equals(null))
            ;
        return $qb;
    }


    /**
     * @param \DateTime $expireDate
     * @return Builder
     */
    public function getQueryBuilderSortWithRank(\DateTime $expireDate = null){
        if ($expireDate == null){
            $expireDate = new \DateTime();
        }
        $qb = $this->createQueryBuilder()
            ->field("rankPosition")->exists(true)
            ->field("expireDate")->gte($expireDate)
            ->field("softDelete")->notEqual(true)
            ->sort(array("rankPosition" => "asc", "finalScore" => "desc"))
        ;
        return $qb;
    }

    /**
     * @return Builder
     */
    public function getPublicQueryBuilderSortWithRank(){
        $qb = $this->getQueryBuilderSortWithRank()
            ->field("publishStatus")->equals("published");
        return $qb;
    }
}
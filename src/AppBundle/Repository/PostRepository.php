<?php

namespace AppBundle\Repository;

use AppBundle\Document\Facebook\FacebookFeed;
use AppBundle\Document\MnemonoBiz;
use AppBundle\Document\Post;
use AppBundle\Document\Weibo\WeiboFeed;
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
     * @param WeiboFeed $feed
     * @return Post|null
     */
    public function findOneByWeiboFeed(WeiboFeed $feed){
        $post = $this->createQueryBuilder()
            ->field("importFrom")->equals("weiboPage")
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
     * @return Builder
     */
    public function getQueryBuilderFindNonExpire(\DateTime $expireDate = null, $limit = 100, $skip = 0){
        if ($expireDate == null){
            $expireDate = new \DateTime();
        }
        $qb = $this->createQueryBuilder()
            ->field("expireDate")->gte($expireDate)
            ->field("softDelete")->notEqual(true)
            ->skip($skip);
        if ($limit >= 0){
            $qb->limit($limit);
        }
        return $qb;
    }

    /**
     * @param MnemonoBiz $biz
     * @param \DateTime $expireDate
     * @param int $limit
     * @param int $skip
     * @return Builder
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
     * @return Builder
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

    /**
     * @param string $tag (in string)
     * @param array $areaCodes array of areaCode (in string)
     * @return int
     */
    public function queryCountOfTagedPost($tag, $areaCodes){
        $qb = $this->getQueryBuilderFindNonExpire(null,-1,0);
        if (!empty($tag)){
            $qb->field('tags')->in(array($tag));
        }
        if (!empty($areaCodes) && is_array($areaCodes)){
            $qb->field('cities')->in($areaCodes);
        }

        $count = $qb->count()->getQuery()->execute();
        return $count;
    }

    /**
     * @param \DateTime $fromDate
     * @param \DateTime $toDate
     * @return Builder
     */
    public function getQueryBuilderFindAllByDate(\DateTime $fromDate, \DateTime $toDate){
        $qb = $this->createQueryBuilder();

        $qb->addAnd(
            $qb->expr()->field("createAt")->gte($fromDate)
        );
        $qb->addAnd(
            $qb->expr()->field("createAt")->lte($toDate)
        );
        return $qb;
    }
}
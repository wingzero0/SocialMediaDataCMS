<?php
/**
 * User: kit
 * Date: 10/29/2015
 * Time: 1:59 PM
 */

namespace AppBundle\Repository;
use AppBundle\Document\Weibo\WeiboPage;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use AppBundle\Document\Facebook\FacebookPage;
use AppBundle\Document\MnemonoBiz;

class MnemonoBizRepository extends DocumentRepository{
    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int $limit
     * @param int $skip
     * @return Builder
     */
    public function getQueryBuilderFindAllByDateRange(\DateTime $startDate, \DateTime $endDate, $limit = 100, $skip = 0){
        $qb = $this->getQueryBuilderFindAll($limit, $skip)
            ->field("lastPostUpdateAt")->gte($startDate)
            ->field("lastPostUpdateAt")->lte($endDate);
        return $qb;
    }

    /**
     * @param int $limit
     * @param int $skip
     * @return Builder
     */
    public function getQueryBuilderFindAll($limit = 100, $skip = 0){
        $qb = $this->createQueryBuilder()
            ->skip($skip)->limit($limit);
        return $qb;
    }

    /**
     * @param FacebookPage $page
     * @return MnemonoBiz|null
     */
    public function findOneByFbPage(FacebookPage $page){
        $biz = $this->createQueryBuilder()
            ->field("importFrom")->equals("facebookPage")
            ->field("importFromRef")->references($page)
            ->getQuery()->getSingleResult();
        return $biz;
    }

    /**
     * @param WeiboPage $page
     * @return MnemonoBiz|null
     */
    public function findOneByWeiboPage(WeiboPage $page){
        $biz = $this->createQueryBuilder()
            ->field("importFrom")->equals("weiboPage")
            ->field("importFromRef")->references($page)
            ->getQuery()->getSingleResult();
        return $biz;
    }
}
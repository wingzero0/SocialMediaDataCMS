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
use Doctrine\ODM\MongoDB\Query\Query;
use AppBundle\Document\Facebook\FacebookPage;
use AppBundle\Document\MnemonoBiz;
use MongoDB\BSON\Regex;

class MnemonoBizRepository extends DocumentRepository
{
    use Prime;

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int $limit
     * @param int $skip
     * @return Builder
     */
    public function getQueryBuilderFindAllByDateRange(\DateTime $startDate, \DateTime $endDate, $limit = 100, $skip = 0)
    {
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
        /* @var MnemonoBiz $biz */
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
        /* @var MnemonoBiz $biz */
        $biz = $this->createQueryBuilder()
            ->field("importFrom")->equals("weiboPage")
            ->field("importFromRef")->references($page)
            ->getQuery()->getSingleResult();
        return $biz;
    }

    /**
     * @param string $q
     * @return Query
     */
    public function getSearchQuery($q)
    {
        $keywords = explode(' ', $q);
        $regex = [];
        foreach ($keywords as $keyword)
        {
            if (!empty($keyword))
            {
                $regex[] = new Regex(trim($keyword), 'i');
            }
        }
        $qb = $this->createQueryBuilder();
        if (!empty($regex))
        {
            $qb->addOr($qb->expr()->field('name')->all($regex));
            $qb->addOr($qb->expr()->field('shortDesc')->all($regex));
            $qb->addOr($qb->expr()->field('longDesc')->all($regex));
        }
        $qb->sort(['lastPostUpdateAt' => -1]);
        $query = $qb->getQuery();
        return $query;
    }

    public function getHomeCount()
    {
        $count = [
            'all' => 0,
            'city_hk' => 0,
            'city_mo' => 0,
            'ref_fb' => 0,
            'ref_weibo' => 0,
        ];
        $qb = $this->createQueryBuilder();
        $count['all'] = $qb->getQuery()
            ->execute()
            ->count();
        $qb = $this->createQueryBuilder();
        $count['city_hk'] = $qb->field('cities')->equals('hk')
            ->getQuery()
            ->execute()
            ->count();
        $qb = $this->createQueryBuilder();
        $count['city_mo'] = $qb->field('cities')->equals('mo')
            ->getQuery()
            ->execute()
            ->count();
        $qb = $this->createQueryBuilder();
        $count['ref_fb'] = $qb
            ->field('importFrom')->equals(MnemonoBiz::FACEBOOK_PAGE)
            ->getQuery()
            ->execute()
            ->count();
        $qb = $this->createQueryBuilder();
        $count['ref_weibo'] = $qb
            ->field('importFrom')->equals(MnemonoBiz::WEIBO_PAGE)
            ->getQuery()
            ->execute()
            ->count();
        return $count;
    }

    /**
     * @param string[] $keywords
     * @return MnemonoBiz[]
     */
    public function findByNameRegularExpression($keywords)
    {
        $regex = array();
        foreach ($keywords as $keyword)
        {
            if (!empty($keyword))
            {
                $regex[] = new Regex(trim($keyword), 'i');
            }
        }
        $qb = $this->createQueryBuilder();
        $bizs = $qb->field('name')->all($regex)->getQuery()->execute();
        return $bizs;
    }
}

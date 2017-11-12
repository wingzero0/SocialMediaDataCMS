<?php

namespace AppBundle\Repository\Facebook;

use AppBundle\Document\Facebook\FacebookPage;
use AppBundle\Document\Facebook\FacebookPageTimestamp;
use Doctrine\ODM\MongoDB\DocumentRepository;

class FacebookPageTimestampRepository extends DocumentRepository
{
    /**
     * @param FacebookPage $page
     * @param \DateTime $start
     * @param \DateTime $end
     * @return FacebookPageTimestamp|null
     */
    public function findAllByPageAndTimeRange(FacebookPage $page,
                                              \DateTime $start,
                                              \DateTime $end)
    {
        return $this->createQueryBuilder()
            ->field("fbPage")->references($page)
            ->field("updateTime")->gte($start)
            ->field("updateTime")->lte($end)
            ->sort('id', 'desc')
            ->getQuery()
            ->execute();
    }
}

<?php
/**
 * User: kit
 * Date: 17/10/15
 * Time: 3:35 PM
 */

namespace AppBundle\Repository\Settings;

use Doctrine\ODM\MongoDB\DocumentRepository;
use AppBundle\Document\Settings\Weighting;

class WeightingRepository extends DocumentRepository {

    /**
     * @param $name
     * @return Weighting|null
     */
    public function findOneByName($name){
        $qb = $this->createQueryBuilder()->field("name")->equals($name);
        $cursor = $qb->getQuery()->execute();
        if ($cursor->count(true)){
            foreach($cursor as $weighting){
                if ($weighting instanceof Weighting){
                    return $weighting;
                }
            }
        }
        return null;
    }

    /**
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    public function getBaseQueryBuilder(){
        return $this->createQueryBuilder();
    }
}
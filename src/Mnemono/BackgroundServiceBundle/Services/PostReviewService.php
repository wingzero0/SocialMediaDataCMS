<?php
/**
 * User: kit
 * Date: 25/12/15
 * Time: 4:06 PM
 */

namespace Mnemono\BackgroundServiceBundle\Services;

use AppBundle\Document\Post;
use Mnemono\BackgroundServiceBundle\Services\BaseService;
use AppBundle\Document\MnemonoBiz;
use Mmoreram\GearmanBundle\Driver\Gearman;

/**
 * @Gearman\Work(
 *     iterations = 1000,
 *     description = "calculate rank of the post in the biz",
 *     defaultMethod = "doBackground",
 *     service="PostReviewService"
 * )
 */
class PostReviewService extends BaseService{
    private $endDate;
    private $startDate;

    /**
     * Job for create post form fbID
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     iterations = 1000,
     *     name = "rank",
     *     description = "calculate rank of the post in the target biz"
     * )
     */
    public function rankPost(\GearmanJob $job){
        try{
            $key_json = json_decode($job->workload(), true);
            $this->resetDM();
            $this->resetDateRange();
            $biz = $this->getMnemenoBizRepo()->find($key_json["id"]);
            if (!$biz instanceof MnemonoBiz){
                $biz = null;
            }
            $updatedPosts = $this->getPostQueryBuilder($biz, 0, 0)->getQuery()->execute();

            $i = 1;
            $dm = $this->getDM();
            foreach($updatedPosts as $post){
                if ($post instanceof Post){
                    if ($biz == null){
                        $post->setRankPosition(1);
                    }else{
                        $post->setRankPosition($i);
                    }
                    $post->setPublishStatus("published");
                    $dm->persist($post);
                    $i++;
                }
            }
            $dm->flush();
            return true;
        }catch (\Exception $e){
            $this->logExecption($e);
            exit(-1);
        }
    }


    /**
     * @param MnemonoBiz $biz
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\MongoDB\Query\Builder
     */
    private function getPostQueryBuilder(MnemonoBiz $biz = null, $limit, $skip){
        $postRepo = $this->getPostRepo();
        $qb = null;
        if ($biz == null){
            $qb = $postRepo->getQueryBuilderFindNonExpireWithBizNotExisit($this->endDate,$limit,$skip);
        }else{
            $qb = $postRepo->getQueryBuilderFindNonExpireByBiz($biz, $this->endDate, $limit, $skip);
        }
        return $qb->field("finalScore")->exists(true)
            ->field("finalScore")->notEqual(null)
            ->sort("finalScore", "desc");
    }

    /**
     * @return int
     */
    private function getDateRangeParameter(){
        return 3;
    }
    private function resetDateRange(){
        $dateRangeParameter = $this->getDateRangeParameter();
        $this->endDate = new \DateTime();
        $this->startDate = clone($this->endDate);
        $this->startDate->sub(new \DateInterval('P'.$dateRangeParameter."D"));
    }
}
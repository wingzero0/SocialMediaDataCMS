<?php
/**
 * User: kit
 * Date: 10/01/16
 * Time: 9:44 PM
 */

namespace CodingGuys\ApiBundle\Controller;

use AppBundle\Controller\AppBaseController;
use AppBundle\Document\ManagedTag;
use AppBundle\Document\Post;
use AppBundle\Proto\AdsDataProto;
use AppBundle\Proto\HomeDataProto;
use AppBundle\Proto\OpenAppRequestProto;
use AppBundle\Proto\PostProto;
use AppBundle\Proto\PostsDataProto;
use AppBundle\Proto\TagWithCountDataProto;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\ODM\MongoDB\Query\Builder;
/**
 * Api Post controller.
 *
 * @Route("/posts")
 */
class PostController extends AppBaseController{

    public function __construct(Container $container = null){
        $this->setContainer($container);
    }
    /**
     * @ApiDoc(
     *  description="home page feed",
     *  parameters={
     *      {"name"="days", "dataType"="int", "required"=false, "description"="filter post within x days"},
     *      {"name"="limit", "dataType"="int", "required"=false, "description"="return x posts, default is 25"},
     *      {"name"="skip", "dataType"="int", "required"=false, "description"="skip first x posts, default is 0"},
     *      {"name"="areaCode", "dataType"="string", "required"=false, "description"="filter with area code, will not support after version 1.0"},
     *      {"name"="areaCodes[]", "dataType"="string", "required"=false, "description"="filter by multiple area codes with 'OR' operator , available at version 1.0"},
     *  }
     * )
     * @Route("/hot", name="api_homepage_post")
     * @Method("GET")
     */
    public function indexAction(Request $request){
 

        $qb = $this->createQueryBuilder($request);
        $qb->field('showAtHomepage')->equals(true);
        $posts = $qb->getQuery()->execute();
        $ret = array();
        foreach($posts as $post){
            $ret[] = $post;
        }
        $serialize = $this->serialize($ret, "display");

        return new Response($this->OutputFormat($request, $serialize));
    }

    /**
     * @ApiDoc(
     *  description="home page data",
     *  parameters={
     *      {"name"="areaCodes[]", "dataType"="string", "required"=false, "description"="filter by multiple area codes with 'OR' operator , available at version 1.0"},
     *  }
     * )
     * @Route("/home", name="api_homepage_data")
     * @Method("GET")
     */
    public function homeAction(Request $request){

        $postsRespond = $this->forward('app.post_controller:indexAction', array("request" => $request));
        $adsRespond = $this->forward('app.spotlight_ads_controller:indexAction', array("request" => $request));
        $tagRespond = $this->forward('app.managed_tag_controller:getManagedTagsAction', array("request" => $request));

        $postsSerialize = $postsRespond->getContent();
        $postsArr = json_decode($postsSerialize,true);

        $tagSerialize = $tagRespond->getContent();
        $tagArr = json_decode($tagSerialize,true);
        $adsSerialize = $adsRespond->getContent();
        $adsArr = json_decode($adsSerialize,true);

        $data = new HomeDataProto();


        $data->setAdsData(AdsDataProto::fromArray($adsArr));
        $data->setPostsData(PostsDataProto::fromArray($postsArr));
        $data->setTagData(TagWithCountDataProto::fromArray($tagArr));

        return new Response(base64_encode($data->toStream()));
    }


    /**
     * @ApiDoc(
     *  description="query all post, can be filter by tags, areaCode",
     *  parameters={
     *      {"name"="tags[]", "dataType"="string", "required"=false, "description"="filter by tag, will not support areaCode in tags after version 1.0"},
     *      {"name"="days", "dataType"="int", "required"=false, "description"="filter post within x days"},
     *      {"name"="limit", "dataType"="int", "required"=false, "description"="return x posts, default is 25"},
     *      {"name"="skip", "dataType"="int", "required"=false, "description"="skip first x posts, default is 0"},
     *      {"name"="areaCode", "dataType"="string", "required"=false, "description"="filter with area code, will not support after version 1.0"},
     *      {"name"="areaCodes[]", "dataType"="string", "required"=false, "description"="filter by multiple area codes with 'OR' operator , available at version 1.0"},
     *  }
     * )
     * @Route("/", name="api_all_post")
     * @Method("GET")
     */
    public function getPostsAction(Request $request){
        $qb = $this->createQueryBuilder($request);
        $posts = $qb->getQuery()->execute();
        $ret = array();
        foreach($posts as $post){
            $ret[] = $post;
        }
        $serialize = $this->serialize($ret, "display");
 
        return new Response($this->OutputFormat($request, $serialize));
    }

    /**
     * @ApiDoc(
     *  description="query a post by id",
     *  requirements={
     *      { "name"="id", "dataType"="string", "requirement"="mongo id in string", "description"="post id"}
     *  }
     * )
     * @Route("/{id}", name="api_specific_post")
     * @Method("GET")
     */
    public function getPostAction(Request $request,$id){
        $post = $this->getPostRepo()->find($id);
        if (!($post instanceof Post)){
            throw $this->createNotFoundException("Unable to find Post document.");
        }
        $serialize = $this->serialize($post, "display");
        return new Response($this->OutputFormat($request, $serialize));
    }

    /**
     * @param Request $request
     * @return Builder
     */
    private function createQueryBuilder(Request $request){
        $qb = $this->getPostRepo()->getPublicQueryBuilderSortWithRank();
        $qb = $this->compileFilter($request, $qb);
        $limit = intval($request->get("limit"));
        if ($limit <= 0){
            $limit = 25;
        }
        $skip = intval($request->get("skip"));
        if ($skip <= 0){
            $skip = 0;
        }
        $qb->limit($limit)->skip($skip);
        return $qb;
    }

    /**
     * @param Request $request
     * @param Builder $qb
     * @return Builder
     */
    private function compileFilter(Request $request, Builder $qb){
        $versionNum = $this->getVersionNum();

        if ($versionNum < 1.0) {
            $qb = $this->compileFilterV0($request, $qb);
        }else{
            $qb = $this->compileFilterV1($request, $qb);
        }
        return $qb;
    }

    /**
     * @param Request $request
     * @param Builder $qb
     * @return Builder
     */
    private function compileFilterV1(Request $request, Builder $qb){
        $qb->field('publishStatus')->equals('published');
        $tags = $request->get('tags');
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $tagTrim = trim($tag);
                if (!empty($tagTrim)) {
                    $qb->addAnd(
                        $qb->expr()->field('tags')->equals($tagTrim)
                    );
                }
            }
        }
        $qb = $this->compileAreaCodeFilter(null, $request->get('areaCodes'), $qb);

        $interval = intval($request->get("days"));
        $qb = $this->compileCreateAtFilter($interval, $qb);
        return $qb;
    }

    /**
     * @param Request $request
     * @param Builder $qb
     * @return Builder
     * @deprecated
     */
    private function compileFilterV0(Request $request, Builder $qb){
        $qb->field('publishStatus')->equals('published');
        $tags = $request->get('tags');
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $tagTrim = trim($tag);
                if (in_array($tagTrim, array("hk", "mo"))){
                    $qb = $this->compileAreaCodeFilter($tagTrim, null, $qb);
                } else if (!empty($tagTrim)) {
                    $qb->addAnd(
                        $qb->expr()->field('tags')->equals($tagTrim)
                    );
                }
            }
        }
        $qb = $this->compileAreaCodeFilter($request->get('areaCode'), null, $qb);

        $interval = intval($request->get("days"));
        $qb = $this->compileCreateAtFilter($interval, $qb);
        return $qb;
    }

    /**
     * @param string $areaCode
     * @param array $areaCodes
     * @param Builder $qb
     * @return Builder
     */
    private function compileAreaCodeFilter($areaCode, $areaCodes, Builder $qb){
        $trimAreaCode = trim($areaCode);
        if (!empty($trimAreaCode)){
            $qb->addOr(
                $qb->expr()->field('cities')->equals($trimAreaCode)
            );
        }
        if (!empty($areaCodes) && is_array($areaCodes)){
            foreach($areaCodes as $code){
                $codeTrim = trim($code);
                if (!empty($codeTrim)){
                    $qb->addOr(
                        $qb->expr()->field('cities')->equals($codeTrim)
                    );
                }
            }
        }
        return $qb;
    }

    /**
     * @param int $interval
     * @param Builder $qb
     * @return Builder
     */
    private function compileCreateAtFilter($interval, Builder $qb){
        $this->getLogger()->info("days");
        if (!empty($interval)) {
            $this->getLogger()->info($interval);
            $nowDate = new \DateTime();
            $createDate = $nowDate->sub(new \DateInterval("P" . $interval . "D"));
            $qb->field("createAt")->gte($createDate);
        }
        return $qb;
    }
    /**
     * @param Request $request
     * @param string $serialize
     * @return string
     */
    public function OutputFormat($request, $serialize){
        $isProto = $request->get('isProto');
        if (!isset($isProto)){
            $isProto = false;
        }
        
        if(!$isProto){
            return $serialize;
        }else{
            $arr = json_decode($serialize,true);

            return base64_encode(PostsDataProto::fromArray($arr)->toStream());
        }
    }
}
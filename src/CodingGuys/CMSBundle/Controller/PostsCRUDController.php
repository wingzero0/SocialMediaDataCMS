<?php
/**
 * Created by PhpStorm.
 * User: codingguys
 * Date: 7/17/15
 * Time: 11:18 AM
 */

namespace CodingGuys\CMSBundle\Controller;

use AppBundle\Controller\AppBaseController;
use AppBundle\Document\Facebook\FacebookFeed;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\Post;
use CodingGuys\CMSBundle\Form\PostType;
use AppBundle\Document\MnemonoBiz;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/dashboard/posts")
 */
class PostsCRUDController extends AppBaseController
{
    /**
     * Displays all the Posts in DB
     *
     * @Route("/", name="posts_home")
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     * @return array variables for template engine
     */
    public function indexAction(Request $request)
    {
        // TODO merge searchAction or remove search action
        $limit = 12;
        $page = intval($request->get('page', 1));
        $q = trim($request->get('q', ''));

        $postRepo = $this->getPostRepo();
        $query = $this->getPostRepo()->getSearchQuery($q);
        $paginator  = $this->getKnpPaginator();
        /* @var SlidingPagination $items */
        $items = $paginator->paginate($query, $page, $limit);
        $postRepo->primeReferences($items, ['mnemonoBiz']);
        return [
            'items' => $items,
            'q' => $q,
        ];

    }

    /**
     * Search MnemonoBiz documents by query.
     *
     * @Route("/search", name="posts_search")
     * @Method("GET")
     * @Template("CodingGuysCMSBundle:PostsCRUD:index.html.twig")
     *
     * @param Request $request
     * @return array variables for template engine
     */
    public function searchAction(Request $request)
    {
        $limit = 15;
        $page = intval($request->get('page', 1));

        $keywords = explode(' ', $request->get('q'));
        $regex = array();
        $postsID = array();
        foreach ($keywords as $keyword)
        {
            $keyword = trim($keyword);
            if (1 === preg_match('/^[0-9a-f]{24}$/', $keyword))
            {
                $postsID[] = $keyword;
            } else if (!empty($keyword))
            {
                $regex[] = $keyword;
            }
        }

        $mnemonobizID = array();
        if (!empty($regex))
        {
            $bizs = $this->getMnemenoBizRepo()->findByNameRegularExpression($regex);
            foreach ($bizs as $biz)
            {
                $mnemonobizID[] = $biz->getId();
            }
        }

        $qbPost = $this->getPostRepo()->createQueryBuilder();
        if (!empty($postsID))
        {
            $qbPost->addOr($qbPost->expr()->field('id')->in($postsID));
        }
        if (!empty($regex))
        {
            $qbPost->addOr($qbPost->expr()->field('content')->all($regex));
        }
        if (!empty($mnemonobizID))
        {
            $qbPost->addOr($qbPost->expr()->field('mnemonoBiz.id')->in($mnemonobizID));
        }
        $query = $qbPost->sort(['createAt' => -1])->getQuery();
        /* @var SlidingPagination $items */
        $items = $this->getKnpPaginator()->paginate($query, $page, $limit);
        $this->getPostRepo()->primeReferences($items, ['mnemonoBiz']);

        return array(
            'items' => $items,
            'q' => $request->get('q'),
        );

    }

    /**
     * Create a Post manually
     *
     * @Route("/create", name="posts_create")
     * @Method({"GET","POST"})
     * @Template("CodingGuysCMSBundle:PostsCRUD:form.html.twig")
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $document = new Post();
        $newForm = $this->createNewForm($document);

        $newForm->handleRequest($request);

        if ($newForm->isValid())
        {
            $this->updatePostFinalScore($document);
            $createTime = new \DateTime();
            $document->setCreateAt($createTime);
            $document->setUpdateAt($createTime);
            $dm = $this->getDM();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('posts_home'));
        }

        return [
            'header' => "Create Post",
            'form' => $newForm->createView(),
            'post' => null,
        ];
    }

    /**
     * Displays a form to edit an existing Post document.
     *
     * @Route("/{id}/edit", name="posts_edit")
     * @Method({"GET","PUT"})
     * @Template("CodingGuysCMSBundle:PostsCRUD:form.html.twig")
     *
     * @param Request $request
     * @param string $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        $document = $this->getPostRepo()->find($id);

        if (!($document instanceof Post))
        {
            throw $this->createNotFoundException('Unable to find Post document.');
        }

        $backupBiz = $document->getMnemonoBiz();

        $editForm = $this->createEditForm($document);

        $editForm->handleRequest($request);
        if ($editForm->isValid())
        {
            $this->updatePostFinalScore($document);
            if ($backupBiz instanceof MnemonoBiz)
            {
                $document->setMnemonoBiz($backupBiz);
            }

            $dm = $this->getDM();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('posts_edit',array('id' => $id)));
        }

        return [
            'header' => "Edit Post",
            'form' => $editForm->createView(),
            'post' => $document,
        ];

    }

    /**
     * Deletes a Post document.
     *
     * @Route("/{id}", name="posts_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $dm = $this->getDM();
            $document = $this->getPostRepo()->find($id);

            if (!$document)
            {
                throw $this->createNotFoundException('Unable to find Post document.');
            }

            $dm->remove($document);
            $dm->flush();
            return $this->redirect($this->generateUrl('posts_home'));
        }
        return new JsonResponse(array("ret" => "delete fail"));
    }

    /**
     * set or unset a post as a spotlight post
     *
     * @Route("/{id}/spotlight", name="posts_spotlight")
     * @Method({"PUT"})
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function spotlightAction(Request $request, $id)
    {
        $document = $this->getPostRepo()->find($id);

        if (!$document instanceof Post)
        {
            throw $this->createNotFoundException('Unable to find Post document.');
        }

        $setFlag = intval($request->get("set"));

        if ($setFlag > 0)
        {
            $document->setSpotlight(true);
            $ret = array("spotlight" => true);
        }
        else
        {
            $document->setSpotlight(false);
            $ret = array("spotlight" => false);
        }

        $this->getDM()->persist($document);
        $this->getDM()->flush();

        return new JsonResponse($ret);
    }

    /**
     * set or unset a post as a homepage post
     *
     * @Route("/{id}/homepage", name="posts_set_homepage")
     * @Method({"PUT"})
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function setHomepageAction(Request $request, $id)
    {
        $document = $this->getPostRepo()->find($id);

        if (!$document instanceof Post)
        {
            throw $this->createNotFoundException('Unable to find Post document.');
        }

        $setFlag = intval($request->get("set"));

        if ($setFlag > 0)
        {
            $document->setShowAtHomepage(true);
            $ret = array("homepage" => true);
        }
        else
        {
            $document->setShowAtHomepage(false);
            $ret = array("homepage" => false);
        }

        $this->getDM()->persist($document);
        $this->getDM()->flush();

        return new JsonResponse($ret);
    }

    /**
     * set a post publishStatus as published or not (means reviewed)
     *
     * @Route("/{id}/publish", name="posts_publish")
     * @Method({"PUT"})
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function publishAction(Request $request, $id)
    {
        $document = $this->getPostRepo()->find($id);

        if (!$document instanceof Post)
        {
            throw $this->createNotFoundException('Unable to find Post document.');
        }

        $setFlag = intval($request->get("set"));

        $status = Post::STATUS_REVIEW;
        if ($setFlag > 0)
        {
            $status = Post::STATUS_PUBLISHED;
        }
        $document->setPublishStatus($status);

        $this->getDM()->persist($document);
        $this->getDM()->flush();
        $ret = array("status" => $status);

        return new JsonResponse($ret);
    }

    /**
     * show a raw data of Post source.
     *
     * @Route("/{id}/sourceRaw", name="posts_source_raw")
     * @Method("GET")
     * @Template()
     *
     * @param string $id
     * @return array variables for template engine
     */
    public function sourceRawAction($id)
    {
        $post = $this->getPostRepo()->find($id);

        if (! $post instanceof Post)
        {
            throw $this->createNotFoundException('Unable to find Post document.');
        }

        $sourceObj = $post->getImportFromRef();
        $ret = $this->queryRawData($sourceObj);
        $strOutput = print_r($ret["rawData"], true);
        return [
            'possibleLinks' => $ret['possibleLinks'],
            'strOutput' => $strOutput,
            'post' => $post,
        ];
    }

    private function queryRawData($obj)
    {
        $possibleLinks = array();
        if ($obj instanceof FacebookFeed)
        {
            $rawData = $this->getFacebookFeedRepo()->getRawById($obj->getId());

            if (isset($rawData["link"]))
            {
                $possibleLinks[] = $rawData["link"];
            }
            $possibleLinks[] = $obj->getShortLink();
            return array("possibleLinks" => $possibleLinks, "rawData" => $rawData);
        }
        return array("possibleLinks" => $possibleLinks, "rawData" => null);
    }

    /**
     * Creates a form to edit a Post document.
     *
     * @param Post $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createNewForm(Post $document)
    {
        $defaultExpireDate = new \DateTime();
        $defaultExpireDate->add(new \DateInterval("P7D"));
        $document->setExpireDate($defaultExpireDate);
        $form = $this->createForm(PostType::class, $document, array(
            'action' => $this->generateUrl('posts_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a form to edit a Post document.
     *
     * @param Post $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Post $document)
    {
        $form = $this->createForm(PostType::class, $document, array(
            'action' => $this->generateUrl('posts_edit', array('id' => $document->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * Creates a form to delete a Post document by id.
     *
     * @param mixed $id The document id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('posts_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Hard Delete'))
            ->getForm()
            ;
    }

    private function updatePostFinalScore(Post $post){
        $localWeight = $this->getWeighting("localWeight");
        $adminWeight = $this->getWeighting("adminWeight");

        $finalScore = $post->updateFinalScore($localWeight, $adminWeight);
        return $finalScore;
    }

    /**
     * Show a post's snapshots
     *
     * @Route("/{id}/snapshots", name="posts_show_snapshots")
     * @Method("GET")
     * @Template("CodingGuysCMSBundle:PostsCRUD:snapshots.html.twig")
     *
     * @param string $id
     * @return array variables for template engine
     */
    public function showSnapshotsAction($id)
    {
        $post = $this->getPostRepo()->find($id);
        if (!$post)
        {
            throw $this->createNotFoundException('Unable to find Mnemono Post.');
        }
        $ref = $post->getImportFromRef();
        $end = new \DateTime();
        $start = clone $end;
        $interval = 'P7D';
        $start->sub(new \DateInterval($interval));
        $snapshots = [];
        if ($ref instanceof FacebookFeed)
        {
            $snapshots = $this->getFacebookFeedTimestampRepo()
                ->findAllByFeedAndTimeRange($ref, $start, $end);
        }
        return [
            'post' => $post,
            'start' => $start,
            'end' => $end,
            'snapshots' => $snapshots,
        ];
    }

    /**
     * add a tag to specified post
     *
     * @Route("/{id}/add-tag", name="post_add_tag")
     * @Method({"POST"})
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function addTagAction(Request $request, $id)
    {
        if (!$this->isCsrfTokenValid('ajax-request', $request->get('csrf-token')))
        {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }
        $post = $this->getPostRepo()->find($id);
        if (!$post instanceof Post)
        {
            throw $this->createNotFoundException('Unable to find Post document.');
        }
        $tag = trim($request->get('tag'));
        $isOk = $post->addTag($tag);
        $post->setUpdateAt(new \DateTime());
        $this->getDM()->persist($post);
        $this->getDM()->flush();
        return new JsonResponse([
            'ok' => $isOk,
            'tags' => $post->getTags(),
        ]);
    }

    /**
     * remove a tag from specified post
     *
     * @Route("/{id}/remove-tag", name="post_remove_tag")
     * @Method({"POST"})
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function removeTagAction(Request $request, $id)
    {
        if (!$this->isCsrfTokenValid('ajax-request', $request->get('csrf-token')))
        {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }
        $post = $this->getPostRepo()->find($id);
        if (!$post instanceof Post)
        {
            throw $this->createNotFoundException('Unable to find Post document.');
        }
        $tag = trim($request->get('tag'));
        $isOk = $post->removeTag($tag);
        $post->setUpdateAt(new \DateTime());
        $this->getDM()->persist($post);
        $this->getDM()->flush();
        return new JsonResponse([
            'ok' => $isOk,
            'tags' => $post->getTags(),
        ]);
    }

    /**
     * set expiration date of specified post
     *
     * @Route("/{id}/set-expn-date", name="post_set_expn")
     * @Method({"POST"})
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function setExpnAction(Request $request, $id)
    {
        if (!$this->isCsrfTokenValid('ajax-request', $request->get('csrf-token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }
        $post = $this->getPostRepo()->find($id);
        if (!$post instanceof Post)
        {
            throw $this->createNotFoundException('Unable to find Post document.');
        }
        $expn = trim(preg_replace('/(\s+)/', ' ', $request->get('expn', '')));
        $format = 'Y-m-d H:i';
        $expnDate = \DateTime::createFromFormat($format, $expn);
        $isOk = $expnDate instanceof \DateTime;
        if (false === $isOk)
        {
            return new JsonResponse([
                'ok' => $isOk,
            ]);
        }
        $post->setExpireDate($expnDate);
        $post->setUpdateAt(new \DateTime());
        $this->getDM()->persist($post);
        $this->getDM()->flush();
        return new JsonResponse([
            'ok' => $isOk,
            'expn' => $expnDate->format($format),
        ]);
    }

    /**
     * Show a post's stats
     *
     * @Route("/{id}/stats", name="posts_show_stats")
     * @Method("GET")
     * @Template("CodingGuysCMSBundle:PostsCRUD:stats.html.twig")
     */
    public function showStatsAction($id)
    {
        $post = $this->getPostRepo()->find($id);
        if (!$post)
        {
            throw $this->createNotFoundException('Unable to find Mnemono Post.');
}
        $refId = new \MongoId($post->getImportFromRef()->getId());
        $end = new \DateTime();
        $start = $post->getCreateAt();
        $postLikeItems = $this->getPostStatsRepo()
            ->findAllByRefId($refId);
        $items = [];
        $d = clone $end;
        while ($d >= $start)
        {
            $key = $d->format('Y-m-d');
            $items[$key] = [
                'postLike' => '---',
                'postComment' => '---',
                'postShare' => '---',
            ];
            $d->sub(new \DateInterval('P1D'));
        }
        $max = [
            'postLike' => null,
            'postComment' => null,
            'postShare' => null,
        ];
        $min = [
            'postLike' => null,
            'postComment' => null,
            'postShare' => null,
        ];
        foreach ($postLikeItems as $item)
        {
            $k = $item->getId()['date'];
            $v = $item->getValue();
            if ($v['today']['updated_at'])
            {
                $items[$k]['postLike'] = $v['today']['like'] - $v['yesterday']['like'];
                $max['postLike'] = is_null($min['postLike']) ?
                    $items[$k]['postLike'] : max($max['postLike'], $items[$k]['postLike']);
                $min['postLike'] = is_null($min['postLike']) ?
                    $items[$k]['postLike'] : min($min['postLike'], $items[$k]['postLike']);
                $items[$k]['postComment'] = $v['today']['comment'] - $v['yesterday']['comment'];
                $max['postComment'] = is_null($min['postComment']) ?
                    $items[$k]['postComment'] : max($max['postComment'], $items[$k]['postComment']);
                $min['postComment'] = is_null($min['postComment']) ?
                    $items[$k]['postComment'] : min($min['postComment'], $items[$k]['postComment']);
                $items[$k]['postShare'] = $v['today']['share'] - $v['yesterday']['share'];
                $max['postShare'] = is_null($min['postShare']) ?
                    $items[$k]['postShare'] : max($max['postShare'], $items[$k]['postShare']);
                $min['postShare'] = is_null($min['postShare']) ?
                    $items[$k]['postShare'] : min($min['postShare'], $items[$k]['postShare']);
            }
        }
        return [
            'post' => $post,
            'start' => $start,
            'end' => $end,
            'items' => $items,
            'max' => $max,
            'min' => $min,
        ];
    }
}

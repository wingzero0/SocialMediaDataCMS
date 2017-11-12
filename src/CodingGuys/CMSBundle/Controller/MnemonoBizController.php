<?php
/**
 * Created by PhpStorm.
 * User: codingguys
 * Date: 7/9/15
 * Time: 1:57 PM
 */

namespace CodingGuys\CMSBundle\Controller;

use AppBundle\Controller\AppBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\MnemonoBiz;
use CodingGuys\CMSBundle\Form\MnemonoBizType;
use AppBundle\Document\Facebook\FacebookPage;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/dashboard/mnemonobiz")
 */
class MnemonoBizController extends AppBaseController
{
    /**
     * @Route("/", name="mnemonobiz_home")
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $limit = 15;
        $page = intval($request->get('page', 1));
        $q = trim($request->get('q', ''));
        $bizRepo = $this->getMnemenoBizRepo();
        $query = $bizRepo->getSearchQuery($q);
        $paginator = $this->getKnpPaginator();
        /* @var MnemonoBiz[] $items */
        $items = $paginator->paginate($query, $page, $limit);
        $bizRepo->primeReferences($items, ['importFromRef']);
        return [
            'items' => $items,
            'q' => $q,
        ];
    }

    /**
     * Displays a form to create a new MnemonoBiz document.
     *
     * @Route("/new", name="mnemonobiz_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $document = new MnemonoBiz();
        $form   = $this->createCreateForm($document);

        return [
            'header' => "Create Biz",
            'document' => $document,
            'form'   => $form->createView(),
            'biz' => null,
        ];
    }

    /**
     * Creates a form to create a MnemonoBiz document.
     *
     * @param MnemonoBiz $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MnemonoBiz $document)
    {
        $form = $this->createForm(MnemonoBizType::class, $document, array(
            'action' => $this->generateUrl('mnemonobiz_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a new MnemonoBiz document.
     *
     * @Route("/", name="mnemonobiz_create")
     * @Method("POST")
     * @Template("CodingGuysCMSBundle:MnemonoBiz:new.html.twig")
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $document = new MnemonoBiz();
        $form = $this->createCreateForm($document);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em =  $this->getDM();
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('mnemonobiz_edit', array('id' => $document->getId())));
        }

        return [
            'header' => "Failed to Create Biz",
            'document' => $document,
            'form'   => $form->createView(),
            'biz' => null,
        ];
    }

    /**
     * Creates a form to delete a MnemonoBiz document by id.
     *
     * @param string $id The document id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mnemonobiz_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete'))
            ->getForm()
            ;
    }

    /**
     * Deletes a MnemonoBiz document.
     *
     * @Route("/{id}", name="mnemonobiz_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDM();
            $document = $this->getMnemenoBizRepo()->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find MnemonoBiz document.');
            }

            $em->remove($document);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mnemonobiz_home'));
    }

    /**
     * Displays a form to edit an existing MnemonoBiz document.
     *
     * @Route("/{id}/edit", name="mnemonobiz_edit")
     * @Method("GET")
     * @Template("CodingGuysCMSBundle:MnemonoBiz:new.html.twig")
     *
     * @param string $id
     * @return array
     */
    public function editAction($id)
    {
        $document = $this->getMnemenoBizRepo()->find($id);

        if (!$document instanceof MnemonoBiz)
        {
            throw $this->createNotFoundException('Unable to find biz document.');
        }

        $editForm = $this->createEditForm($document);
        $deleteForm = $this->createDeleteForm($id);

        return [
            'header' => 'Edit' . ($document->getName() ? ': ' . $document->getName() : 'Biz'),
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'biz' => $document,
        ];
    }

    /**
     * Creates a form to edit a MnemonoBiz document.
     *
     * @param MnemonoBiz $document The document
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MnemonoBiz $document)
    {
        $form = $this->createForm(MnemonoBizType::class, $document, array(
            'action' => $this->generateUrl('mnemonobiz_update', array('id' => $document->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing MnemonoBiz document.
     *
     * @Route("/{id}", name="mnemonobiz_update")
     * @Method("PUT")
     * @Template("CodingGuysCMSBundle:MnemonoBiz:new.html.twig")
     *
     * @param Request $request
     * @param string $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction(Request $request, $id)
    {
        $document = $this->getMnemenoBizRepo()->find($id);

        if (!$document instanceof MnemonoBiz) {
            throw $this->createNotFoundException('Unable to find MnemonoBiz document.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($document);

        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em = $this->getDM();
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('mnemonobiz_edit', array('id' => $id)));
        }

        return array(
            'header' => "Update MmemonoBiz Failed!",
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Show a biz's snapshots
     *
     * @Route("/{id}/snapshots", name="mnemonobiz_show_snapshots")
     * @Method("GET")
     * @Template("CodingGuysCMSBundle:MnemonoBiz:snapshots.html.twig")
     *
     * @param string $id
     * @return array
     */
    public function showSnapshotsAction($id)
    {
        $biz = $this->getMnemenoBizRepo()->find($id);
        if (!$biz)
        {
            throw $this->createNotFoundException('Unable to find Mnemono Biz.');
        }
        $ref = $biz->getImportFromRef();
        $end = new \DateTime();
        $start = clone $end;
        $interval = 'P7D';
        $start->sub(new \DateInterval($interval));
        $snapshots = [];
        if ($ref instanceof FacebookPage)
        {
            $snapshots = $this->getFacebookPageTimestampRepo()
                ->findAllByPageAndTimeRange($ref, $start, $end);
        }
        return [
            'biz' => $biz,
            'start' => $start,
            'end' => $end,
            'snapshots' => $snapshots,
        ];
    }

    /**
     * Show a biz's stats
     *
     * @Route("/{id}/stats", name="mnemonobiz_show_stats")
     * @Method("GET")
     * @Template("CodingGuysCMSBundle:MnemonoBiz:stats.html.twig")
     *
     * @param string $id
     * @return array
     */
    public function showStatsAction($id)
    {
        $biz = $this->getMnemenoBizRepo()->find($id);
        if (!$biz)
        {
            throw $this->createNotFoundException('Unable to find Mnemono Biz.');
        }
        $refId = new \MongoId($biz->getImportFromRef()->getId());
        $bizId = new \MongoId($id);
        $end = new \DateTime();
        $start = clone $end;
        $interval = 'P30D';
        $start->sub(new \DateInterval($interval));
        $fanItems = $this->getBizStatsRepo()
            ->findAllByTimeRange($refId, $start, $end);
        $postItems = $this->getBizPostCountStatsRepo()
            ->findAllByTimeRange($bizId, $start, $end);
        $postMetricItems = $this->getBizPostMetricStatsRepo()
            ->findAllByTimeRange($refId, $start, $end);
        $items = [];
        $d = clone $end;
        while ($d >= $start)
        {
            $key = $d->format('Y-m-d');
            $items[$key] = [
                'fan' => '---',
                'post' => '---',
                'postLike' => '---',
                'postComment' => '---',
                'postShare' => '---',
            ];
            $d->sub(new \DateInterval('P1D'));
        }
        $max = [
            'fan' => null,
            'post' => null,
            'postLike' => null,
            'postComment' => null,
            'postShare' => null,
        ];
        $min = [
            'fan' => null,
            'post' => null,
            'postLike' => null,
            'postComment' => null,
            'postShare' => null,
        ];
        foreach ($fanItems as $item)
        {
            $k = $item->getId()['date'];
            $v = $item->getValue()['today'];
            if ($v['updated_at'])
            {
                $items[$k]['fan'] = $v['fan'];
                $max['fan'] = is_null($min['fan']) ?
                    $v['fan'] : max($max['fan'], $v['fan']);
                $min['fan'] = is_null($min['fan']) ?
                    $v['fan'] : min($min['fan'], $v['fan']);
            }
        }
        foreach ($postItems as $item)
        {
            $k = $item->getId()['date'];
            $v = $item->getValue();
            $items[$k]['post'] = $v;
            $max['post'] = is_null($min['post']) ?
                $v : max($max['post'], $v);
            $min['post'] = is_null($min['post']) ?
                $v : min($min['post'], $v);
        }
        foreach ($postMetricItems as $item)
        {
            $k = $item->getId()['date'];
            $v = $item->getValue();
            $items[$k]['postLike'] = $v['like'];
            $max['postLike'] = is_null($min['postLike']) ?
                $v['like'] : max($max['postLike'], $v['like']);
            $min['postLike'] = is_null($min['postLike']) ?
                $v['like'] : min($min['postLike'], $v['like']);
            $items[$k]['postComment'] = $v['comment'];
            $max['postComment'] = is_null($min['postComment']) ?
                $v['comment'] : max($max['postComment'], $v['comment']);
            $min['postComment'] = is_null($min['postComment']) ?
                $v['comment'] : min($min['postComment'], $v['comment']);
            $items[$k]['postShare'] = $v['share'];
            $max['postShare'] = is_null($min['postShare']) ?
                $v['share'] : max($max['postShare'], $v['share']);
            $min['postShare'] = is_null($min['postShare']) ?
                $v['share'] : min($min['postShare'], $v['share']);
        }
        return [
            'biz' => $biz,
            'start' => $start,
            'end' => $end,
            'items' => $items,
            'max' => $max,
            'min' => $min,
        ];
    }
}

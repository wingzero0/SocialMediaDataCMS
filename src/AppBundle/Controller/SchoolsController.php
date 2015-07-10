<?php
/**
 * User: kit
 * Date: 04/07/15
 * Time: 16:27
 */

namespace AppBundle\Controller;

use AppBundle\Document\School;
use AppBundle\Controller\CMSBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * @Route("/dashboard/schools")
 */
class SchoolsController extends CMSBaseController{
    /**
     * @ApiDoc(

     * )
     * @Route("/", name="schools_home")
     * @Method("GET")
     */
    public function schoolAction(Request $request){
        $limit = 15;
        $page = intval($request->get('page', 1));
        $classType = $request->get('classType', 'All');

        $dm = $this->get('doctrine_mongodb')->getManager();
        $qb = $dm->createQueryBuilder('AppBundle:School')
            ->field('softDelete')->notEqual(true);
        /*->sort('publishDate', 'desc' );*/
        $query = $qb->getQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            $limit
        );

        return $this->render('AppBundle:schools:index.html.twig', array(
            'pagination' => $pagination,
            /*'classTypeList' => News::getGroupedClassTypeList(),
            'currentFilter' => $classType,*/
        ));
    }
    /**
     * @Route("/form", name="schools_form")
     * @Method("GET|POST")
     */
    public function schoolFormAction(Request $request){
        $id = $request->get('id');
        $school = null;
        if (!empty($id)){
            $school = $this->querySchool($id);
        }else{
            $school = new School();
        }

        if (!$school){
            return $this->render('AppBundle:cms:notFound.html.twig', array());
        }

        return $this->schoolNextOperation($school,$request);

    }

    /**
     * @ApiDoc(
     * )
     * @Route("/{id}", name="schools_delete")
     * @Method("DELETE")
     */
    public function deleteSchoolAction(Request $request, $id){
        $school = $this->querySchool($id);
        if (!$school ){
            return $this->retError($request->get("responseType"), "invalid id");
        }
        $this->deleteSchool($school);
        return $this->retSuccess($request->get("responseType"), "School removed");
    }

    private function deleteSchool(School $school){
        $school->setSoftDelete(true);
        $this->persistNews($school);
    }

    private function schoolNextOperation($school, $request){
        $form = $this->initSchoolForm($school);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /*if ($form->get('saveAndPush')->isClicked()){
                $nextOp = 'cms_push';
            }else{
                $nextOp = 'cms_home';
            }*/
            $nextOp = 'cms_home';
            $id = $this->persistSchool($school);
            return $this->redirect($this->generateUrl($nextOp, array('id'=> $id)));
        }else{
            return $this->renderSchoolForm($school);
        }
    }

    /**
     * get school form response
     *
     * @param School $school a news object
     * @return Response
     */
    private function renderSchoolForm($school){
        $form = $this->initSchoolForm($school);
        if ($school->getId()){
            $heading = 'Edit School Info';
        }else{
            $heading = 'Create School Info';
        }

        return $this->render('AppBundle:schools:new.html.twig', array(
            'form' => $form->createView(),
            'classTypeList' => School::getGroupedClassTypeList(),
            'heading' => $heading,
        ));
    }

    private function persistSchool(School $school){
        /*if (!$school->getId()){
            $school->setPublishDate(new \MongoDate());
        }*/
        $school->convertDynamicFieldsToList();

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($school);
        $dm->flush();
        $id = $school->getId();
        return $id;
    }

    private function initSchoolForm(School $school){
        $builder = $this->createFormBuilder($school)
            ->add('name', 'text')
        ->add('classtype', 'collection', array(
            'label' => 'Class',
                'type' => 'text',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true, 'required' => false));


//        $classTypeList = School::getClassTypeList();
//        foreach ($classTypeList as $className){
//            $builder->add($className, 'checkbox', array('required' => false));
//        }

        $builder->add('save', 'submit', array('label' => 'Save'));

        return $builder->getForm();
    }
    /**
     * query school
     *
     * @param string $id schoolID
     * @return School
     */
    private function querySchool($id){
        $dm = $this->get('doctrine_mongodb')->getManager();
        $school = $dm->createQueryBuilder('AppBundle:School')
            ->field("_id")->equals($id)
            ->field("softDelete")->notEqual(true)
            ->getQuery()
            ->getSingleResult();
        if ($school && $school instanceof School){
            $school->convertListToDynamicFields();
        }

        return $school;
    }

}
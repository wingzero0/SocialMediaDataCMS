<?php
/**
 * Created by PhpStorm.
 * User: codingguys
 * Date: 7/17/15
 * Time: 11:32 AM
 */

namespace CodingGuys\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Document\Post;

class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'text', array(
                'read_only' => true
            ))
            ->add('tags','collection', array(
                'type' => 'text',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false
            ))
            ->add('mnemonoCat', 'text', array(
                'required' => false
            ))
            ->add('lastModDate','date', array(
                'read_only' => true,
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ))
            ->add('rankPosition','number', array(
                'required' => false
            ))
            ->add('localScore','number', array(
                'read_only' => true,
                'required' => false
            ))
            ->add('adminScore','number', array(
                'required' => false
            ))
            ->add('importFrom', 'text', array(
                'read_only' => true
            ))
            ->add('publishStatus', 'choice', array(
                'empty_value' => false,
                'required' => true,
                'choices' => Post::listOfPublishStatus(),
            ))
            ->add('expireDate','datetime', array(
                'required' => true,
            ))
            ->add('content','textarea', array(
                'attr' => array('rows' => '10'),
                'required' => false,
            ))
            ->add('spotlight', 'checkbox', array(
                'label'    => 'Spotlight',
                'required' => false,
            ))
            ->add('softDelete', 'checkbox', array(
                'label'    => 'Delete',
                'required' => false,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Document\Post'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'AppBundle_post';
    }
}
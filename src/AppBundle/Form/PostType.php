<?php
/**
 * Created by PhpStorm.
 * User: codingguys
 * Date: 7/17/15
 * Time: 11:32 AM
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


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
                'required' => false
            ))
            ->add('rankingScoreAlgorithm','number', array(
                'required' => false
            ))
            ->add('rankingScoreHuman','number', array(
                'required' => false
            ))
            ->add('mnemonoBiz', new MnemonoBizType(), array(
                'read_only' => true
            ))
            ->add('importFrom', 'text', array(
                'read_only' => true
            ))
            ->add('publishStatus')
            ->add('content','textarea', array(
                'attr' => array('rows' => '10'),
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
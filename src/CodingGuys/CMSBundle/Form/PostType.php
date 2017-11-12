<?php
/**
 * Created by PhpStorm.
 * User: codingguys
 * Date: 7/17/15
 * Time: 11:32 AM
 */

namespace CodingGuys\CMSBundle\Form;

use AppBundle\Utility\DocumentPath;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Document\Post;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, array(
                'attr' => ['readonly' => true],
            ))
            ->add('tags', CollectionType::class, array(
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false
            ))
            ->add('cities', CollectionType::class, array(
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false
            ))
            ->add('imageLinks', CollectionType::class, array(
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false
            ))
            ->add('videoLinks', CollectionType::class, array(
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false
            ))
            ->add('originalLink', TextType::class, array(
                'required' => false
            ))
            ->add('updateAt', DateType::class, array(
                'attr' => ['readonly' => true],
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ))
            ->add('rankPosition', NumberType::class, array(
                'required' => false
            ))
            ->add('localScore', NumberType::class, array(
                'attr' => ['readonly' => true],
                'required' => false
            ))
            ->add('adminScore', NumberType::class, array(
                'required' => false
            ))
            ->add('finalScore', NumberType::class, array(
                'attr' => ['readonly' => true],
                'required' => false
            ))
            ->add('importFrom', TextType::class, array(
                'attr' => ['readonly' => true],
            ))
            ->add('publishStatus', ChoiceType::class, array(
                'empty_data' => false,
                'required' => true,
                'choices' => Post::listOfPublishStatus(),
            ))
            ->add('expireDate', DateTimeType::class, array(
                'required' => true,
            ))
            ->add('content', TextareaType::class, array(
                'attr' => array('rows' => '10'),
                'required' => false,
            ))
            ->add('showAtHomepage', CheckboxType::class, array(
                'label'    => 'Show at homepage',
                'required' => false,
            ))
            ->add('softDelete', CheckboxType::class, array(
                'label'    => 'Delete',
                'required' => false,
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DocumentPath::$postFolderPath,
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

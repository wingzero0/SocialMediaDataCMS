<?php
/**
 * Created by PhpStorm.
 * User: codingguys
 * Date: 7/9/15
 * Time: 3:39 PM
 */

namespace CodingGuys\CMSBundle\Form;

use AppBundle\Utility\DocumentPath;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MnemonoBizType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('shortDesc', TextType::class, array(
                'required' => false
            ))
            ->add('longDesc', TextType::class, array(
                'required' => false
            ))
            ->add('category', TextType::class, array(
                'required' => false
            ))
            ->add('tags', CollectionType::class, array(
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false
            ))
            ->add('addresses', CollectionType::class, array(
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
            ->add('phones', CollectionType::class, array(
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false
            ))
            ->add('faxes', CollectionType::class, array(
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false
            ))
            ->add('websites', CollectionType::class, array(
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false
            ))
            ->add('weighting', NumberType::class, array(
                'required' => false
            ))
            ->add('importFrom', TextType::class, array(
                'attr' => ['readonly' => true],
            ))
        ;

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DocumentPath::$mnemonoBizFolderPath,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'AppBundle_mnemonoBiz';
    }
}

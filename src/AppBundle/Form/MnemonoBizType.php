<?php
/**
 * Created by PhpStorm.
 * User: codingguys
 * Date: 7/9/15
 * Time: 3:39 PM
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('shortDesc')
            ->add('longDesc')
            ->add('category')
            ->add('tags','collection',array(
                'type' => 'text',
                'allow_add' => true
            ))
            ->add('address')
            ->add('city')
            ->add('phones','collection')
            ->add('faxes','collection')
            ->add('websites','collection')
            ->add('weighting','number')
            ->add('importFrom')
        ;

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Document\MnemonoBiz'
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
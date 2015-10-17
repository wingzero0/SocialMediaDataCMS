<?php
/**
 * User: kit
 * Date: 17/10/15
 * Time: 6:05 PM
 */

namespace CodingGuys\CMSBundle\Form\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class WeightingType extends AbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text',array(
                'required' => true
            ))
            ->add('value','text',array(
                'required' => true
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Document\Settings\Weighting'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'CodingGuysCMSBundle_Weighting';
    }
}
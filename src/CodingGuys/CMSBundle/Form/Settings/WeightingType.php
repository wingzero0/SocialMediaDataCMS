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
use Symfony\Component\Form\Extension\Core\Type\TextType;

class WeightingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'required' => true
            ))
            ->add('value', TextType::class, array(
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

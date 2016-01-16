<?php
/**
 * User: kit
 * Date: 14/01/16
 * Time: 9:56 PM
 */

namespace CodingGuys\CMSBundle\Form;

use AppBundle\Utility\DocumentPath;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpotlightAdsType extends AbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'text', array(
                'required' => false
            ))
            ->add('displaySeq', 'text', array(
                'required' => true
            ))
            ->add('imageLink', 'text', array(
                'required' => false
            ))
            ->add('landingPage', 'text', array(
                'required' => false
            ))
        ;
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DocumentPath::$spotlightAdsFolderPath,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'AppBundle_spotlightAds';
    }
}
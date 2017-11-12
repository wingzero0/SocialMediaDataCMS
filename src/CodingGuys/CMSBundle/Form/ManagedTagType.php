<?php
/**
 * User: kit
 * Date: 8/1/2016
 * Time: 14:08
 */

namespace CodingGuys\CMSBundle\Form;

use AppBundle\Utility\DocumentPath;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class ManagedTagType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('key', TextType::class, ['label' => 'key'])
            ->add('nameChi', TextType::class, ['label' => 'chinese name'])
            ->add('nameEng', TextType::class, ['label' => 'english name'])
            ->add('imageLink', UrlType::class, ['label' => 'image link (starting with http:// or https://)'])
            ->add('displaySeq', IntegerType::class, ['label' => 'sequence (smaller number first)']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DocumentPath::$managedTagFolderPath,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'AppBundle_managedTag';
    }
}

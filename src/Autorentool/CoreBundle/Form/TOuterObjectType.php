<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TOuterObject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TOuterObjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('data', FileType::class, array(
            'label' => false,
            'required' => false,
            'multiple' => false,
            'attr' => array(
                'class' => 'hotspotImgSrc',
                'accept' => 'image/png',
                'style' => 'display:none',
            ),
        ));

        $builder->add('dataOrigName', TextType::class, array(
            'label' => false,
            'attr' => array(
                'class' => 'form-control hotspotImageFileName',
                'readonly' => 'readonly',
            ),
            'required' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TOuterObject::class,
        ));
    }

    public function getName()
    {
        return 'touterobject';
    }
}
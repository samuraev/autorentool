<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TAssessmentItem;
use Autorentool\CoreBundle\Entity\TSupport;
use Autorentool\CoreBundle\Entity\TSupportMedia;
use Autorentool\CoreBundle\Entity\TSupportTable;
use Autorentool\CoreBundle\Form\DataTransformer\SelectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TSupportMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('mediaSource', FileType::class, array(
            'label' => false,
            'required' => false,
            'multiple' => false,
            'attr' => array(
                'class' => 'support-media',
                'accept' => 'video/mp4, image/*',
                'style' => 'display:none;',
            ),
        ));

        $builder->add('mediaSourceOrigName', TextType::class, array(
            'label' => false,
            'attr' => array(
                'class' => 'form-control support-media-filename',
                'readonly' => 'readonly',
            ),
            'required' => false,
        ));

        $builder->add('prompt', TextareaType::class, array(
            'label' => false,
            'attr' => array(
                'class' => 'form-control support-text-description',
                'placeholder' => 'Beschreibung des Mediafiles',
            ),
            'required' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TSupportMedia::class,
        ));
    }

    public function getName()
    {
        return 'tsupportmediatype';
    }
}
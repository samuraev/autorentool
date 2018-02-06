<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TAreaMapEntry;
use Autorentool\CoreBundle\Entity\TMapping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TAreaMapEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('coords', TextType::class, array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'readonly' => 'readonly',
                'class' => 'coordinates',
                'style' => 'display:none;',
            ),
        ));

        $builder->add('shape', ChoiceType::class, array(
            'label' => false,
            'choices' => array('Kreis' => 'circle', 'Ellipse' => 'ellipse', 'Rechteck' => 'rect', 'Vieleck' => 'poly'),
            'required' => true,
            'multiple' => false,
            'attr' => array(
                'style' => 'width:100%;',
            )
        ));

        $builder->add('current', RadioType::class, array(
            'label' => false,
            'attr' => array(
                'class' => 'one-rightanswer',
            ),
            'required' => false,
        ));

        $builder->add('data', TextareaType::class, array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'readonly' => 'readonly',
                'class' => 'imageData',
                'style' => 'display:none;',
                ),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TAreaMapEntry::class,
        ));
    }

    public function getName()
    {
        return 'tmapping';
    }
}
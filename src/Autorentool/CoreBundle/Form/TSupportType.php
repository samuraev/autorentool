<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TAssessmentItem;
use Autorentool\CoreBundle\Entity\TSupport;
use Autorentool\CoreBundle\Entity\TSupportTable;
use Autorentool\CoreBundle\Form\DataTransformer\SelectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TSupportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $supportSelection = $options['selection'];

        $builder->add('supportTextbox', TSupportTextboxType::class, array(
            'label' => false,
        ));

        $builder->add('supportMedia', TSupportMediaType::class, array(
            'label' => false,
        ));

        $builder->add('supportSelection', TSupportSelectionType::class, array(
            'label' => false,
            'selection' => $supportSelection,
        ));

        $builder->add('supportTable', TSupportTableType::class, array(
            'label' => false,
        ));

        $builder->add('supportType',ChoiceType::class, array(
            'attr' => array(
                'class' => 'support-type'
            ),
            'choices' => array(
                'Medienhilfe'  => 'media',
                'Texthilfe'    => 'textbox',
                'Auswahlhilfe' => 'selection',
                'Tabellenhilfe' => 'table'),
            'multiple'=>false,
            'expanded'=>true
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TSupport::class,
            'selection' => null,
        ));
    }

    public function getName()
    {
        return 'tsupporttype';
    }
}
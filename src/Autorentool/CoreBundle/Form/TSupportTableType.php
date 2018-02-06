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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TSupportTableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('row', CollectionType::class, array(
            'label' => false,
            'entry_type' => TSupportRowType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype_name' => '__rowname__',
            'entry_options' => array('label' => false)
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TSupportTable::class,
        ));
    }

    public function getName()
    {
        return 'tsupporttabletype';
    }
}
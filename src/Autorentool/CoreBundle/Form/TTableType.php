<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TChoiceInteraction;
use Autorentool\CoreBundle\Entity\TRow;
use Autorentool\CoreBundle\Entity\TTable;
use Autorentool\CoreBundle\Entity\TTableInteraction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TTableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tasktype = $options['tasktype'];

        $builder->add('row', CollectionType::class, array(
            'label' => false,
            'entry_type' => TRowType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype_name' => '__rowname__',
            'entry_options' => array('label' => false, 'tasktype' => $tasktype)
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TTable::class,
            'tasktype' => null,
        ));
    }

    public function getName()
    {
        return 'ttable';
    }

}
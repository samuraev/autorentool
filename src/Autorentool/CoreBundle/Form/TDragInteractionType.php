<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TChoiceInteraction;
use Autorentool\CoreBundle\Entity\TDragInteraction;
use Autorentool\CoreBundle\Entity\TTable;
use Autorentool\CoreBundle\Entity\TTableInteraction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TDragInteractionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tasktype = $options['tasktype'];

        $builder->add('mode', ChoiceType::class, array(
            'label' => 'Einsortieren in Spalte oder Reihe',
            'choices' => array('Spalte' => 'column', 'Reihe' => 'row'),
            'attr' => array(
                'class' => 'dragtable-mode-select',
                ),
            'required' => false,

        ));

        $builder->add('dragTable', TTableType::class, array(
            'label' => false,
            'tasktype' => $tasktype
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TDragInteraction::class,
            'tasktype' => null
        ));
    }

    public function getName()
    {
        return 'tdraginteraction';
    }

}
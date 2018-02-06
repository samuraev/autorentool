<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TChoiceInteraction;
use Autorentool\CoreBundle\Entity\TTable;
use Autorentool\CoreBundle\Entity\TTableInteraction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TTableInteractionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tasktype = $options['tasktype'];

        $builder->add('table', TTableType::class, array(
            'label' => false,
            'tasktype' => $tasktype
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TTableInteraction::class,
            'tasktype' => null
        ));
    }

    public function getName()
    {
        return 'ttableinteraction';
    }

}
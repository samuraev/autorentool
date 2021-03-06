<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TCell;
use Autorentool\CoreBundle\Entity\TChoiceInteraction;
use Autorentool\CoreBundle\Entity\TRow;
use Autorentool\CoreBundle\Entity\TTable;
use Autorentool\CoreBundle\Entity\TTableInteraction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TSupportCellType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('head', CheckboxType::class, array(
            'label' => 'Überschrift',
            'required' => false,
            'attr' => array(
                'class' => 'cell-el cell-support-head',
            ),
        ));

        $builder->add('value', TextType::class, array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'class' => 'cell-support-value',
                'style' => 'width:100%;'
            ),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TCell::class
        ));
    }

    public function getName()
    {
        return 'tsupportcell';
    }

}
<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TAreaMapping;
use Autorentool\CoreBundle\Entity\TMapping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TAreaMappingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('areaMapEntry', CollectionType::class, array(
            'entry_type' => TAreaMapEntryType::class,
            'entry_options' => array('label' => false),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TAreaMapping::class,
        ));
    }

    public function getName()
    {
        return 'tmapping';
    }
}
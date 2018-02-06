<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\THotspotInteraction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class THotspotInteractionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('outerObject', TOuterObjectType::class, array(
            'label' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => THotspotInteraction::class,
        ));
    }

    public function getName()
    {
        return 'thotspotinteraction';
    }
}
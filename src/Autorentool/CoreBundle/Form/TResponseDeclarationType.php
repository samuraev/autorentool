<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TResponseDeclaration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TResponseDeclarationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tasktype = $options['tasktype'];

        if ($tasktype === 'point') {
            $builder->add('areaMapping', TAreaMappingType::class, array(
                'label' => false,
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TResponseDeclaration::class,
            'tasktype' => null,
        ));
    }

    public function getName()
    {
        return 'tresponsedeclaration';
    }
}
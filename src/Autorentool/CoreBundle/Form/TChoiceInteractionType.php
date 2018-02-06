<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TChoiceInteraction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TChoiceInteractionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tasktype = $options['tasktype'];

        $builder->add('shuffle', CheckboxType::class, array(
            'label' => 'Aufgabenmischung',
            'required' => false,
        ));

        $builder->get('shuffle')
            ->addModelTransformer(new CallbackTransformer(
                function ($shuffleValue) {
                    // transform the int to a bool
                    return boolval($shuffleValue);
                },
                function ($shuffleValue) {
                    // transform the bool back to an integer
                    return (int)$shuffleValue;
                }
            ));

        $builder->add('simpleChoices', CollectionType::class, array(
            'entry_type' => TSimpleChoiceType::class,
            'entry_options' => array('label' => false, 'tasktype' => $tasktype),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TChoiceInteraction::class,
            'tasktype' => null,
        ));
    }

    public function getName()
    {
        return 'tchoiceiteraction';
    }

}
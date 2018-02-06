<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TSimpleChoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TSimpleChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tasktype = $options['tasktype'];

        $builder->add('caption', TextareaType::class, array(
            'label' => false,
            'required' => true,
            'attr' => array(
                'placeholder' => 'Antwort',
                'class' => 'form-control table-question caption'
            ),
        ));

        $builder->add('imgSrc', FileType::class, array(
            'label' => false,
            'required' => false,
            'multiple' => false,
            'attr' => array(
                'class' => 'table-question imgSrc',
                'accept' => 'image/*',
                'style' => 'display:none;',
            ),
        ));

        $builder->add('removeImgSrcState', CheckboxType::class, array(
            'label' => false,
            'required' => false,
            'attr' => array(
                'class' => 'table-question-removeImgSrc',
                'style' => 'display:none;',
            ),
        ));

        if ($tasktype == 'single') {
            $builder->add('isrightanswer', RadioType::class, array(
                'label' => false,
                'attr' => array(
                    'class' => 'one-rightanswer',
                    ),
                'required' => true
            ));
        } elseif ($tasktype == 'multiple') {
            $builder->add('isrightanswer', CheckboxType::class, array(
                'label' => false,
                'attr' => array(
                    'class' => 'many-rightanswer',
                ),
                'required' => true
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TSimpleChoice::class,
            'tasktype' => null,
        ));
    }

    public function getName()
    {
        return 'tsimplechoice';
    }

}
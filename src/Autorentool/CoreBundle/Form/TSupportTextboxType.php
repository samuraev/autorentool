<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TAssessmentItem;
use Autorentool\CoreBundle\Entity\TSupport;
use Autorentool\CoreBundle\Entity\TSupportTable;
use Autorentool\CoreBundle\Entity\TSupportTextbox;
use Autorentool\CoreBundle\Form\DataTransformer\SelectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TSupportTextboxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('textboxContent', TextareaType::class, array(
            'label' => false,
            'attr' => array(
                'class' => 'form-control support-textbox',
                'placeholder' => 'Texthilfe',
            ),
            'required' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TSupportTextbox::class,
        ));
    }

    public function getName()
    {
        return 'tsupporttextboxtype';
    }
}
<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TAssessmentItem;
use Autorentool\CoreBundle\Entity\TSelectionItem;
use Autorentool\CoreBundle\Entity\TSupport;
use Autorentool\CoreBundle\Entity\TSupportSelection;
use Autorentool\CoreBundle\Entity\TSupportTable;
use Autorentool\CoreBundle\Form\DataTransformer\SelectionTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TSupportSelectionType extends AbstractType
{
    private $transformer;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->transformer = new SelectionTransformer();

        $supportSelection = $options['selection'];

        $builder->add('currentSelection', ChoiceType::class, array(
            'label' => false,
            'choices' => $supportSelection,
            'attr' => array(
                'class' => 'chosen-select-support',
                'multiple' => 'multiple',
                'style' => 'width:100%;',
                'data-placeholder' => 'Bereitstellung von einer Auswahlliste',
                'hidden'=> 'hidden',
                'tabindex' => '4',
            ),
            'required' => false,
            'multiple' => true,

        ));

        $builder->get('currentSelection')
            ->addModelTransformer($this->transformer);

        $builder->get('currentSelection')->resetViewTransformers();

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TSupportSelection::class,
            'selection' => null,
        ));
    }

    public function getName()
    {
        return 'tsupportselectiontype';
    }
}
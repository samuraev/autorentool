<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TCategories;
use Autorentool\CoreBundle\Entity\TAssessmentItem;
use Autorentool\CoreBundle\Entity\TCategoryTags;
use Autorentool\CoreBundle\Entity\TSupportTable;
use Autorentool\CoreBundle\Form\DataTransformer\SelectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TAssessmentItemType extends AbstractType
{
    private $transformer;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $raw_categories = $options['categories'];
        $supportSelection = $options['selection'];
        $tasktype = $options['tasktype'];
        $this->em = $options['em'];

        $builder->add('tittle', TextType::class, array(
            'label' => false,
            'attr' => array(
                'placeholder' => 'Titel der Aufgabe',
                'class' => 'form-control tasktittle',
                ),
            'required' => true,
        ));

        $builder->add('itemBody', TItemBodyType::class, array(
            'label' => false,
            'tasktype' => $tasktype
        ));

        $builder->add('responseDeclaration', TResponseDeclarationType::class, array(
            'label' => false,
            'tasktype' => $tasktype,
        ));

        $builder->add('support', TSupportType::class, array(
            'label' => false,
            'selection' => $supportSelection,
        ));

        $categories = [];
        foreach ($raw_categories as $catObj){
            $categories[$catObj->getCategoryName()] = $catObj->getCategoryName();
        }

        $this->transformer = new SelectionTransformer();

        $builder->add('currentCategory', ChoiceType::class, array(
            'label' => false,
            'choices' => $categories,
            'attr' => array('class' => 'chosen-select-category',
                'multiple' => 'multiple',
                'style' => 'width:100%;',
                'data-placeholder' => 'Wählen Sie das Kategorie-Tag aus',
                'hidden'=> 'hidden',
                'tabindex' => '4',
            ),
            'required' => false,
            'multiple' => true,

        ));

        $builder->get('currentCategory')
            ->addModelTransformer($this->transformer);


        $builder->get('currentCategory')->resetViewTransformers();

        $builder->add('stateOfTask', CheckboxType::class, array(
            'label' => 'Zum Export bereitstellen',
            'required' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TAssessmentItem::class,
            'categories' => null,
            'tasktype' => null,
            'selection' => null,
            'em' => null
        ));
    }

    public function getName()
    {
        return 'tassessmentitemtype';
    }
}
<?php

namespace Autorentool\CoreBundle\Form;

use Autorentool\CoreBundle\Entity\TItemBody;
use Autorentool\CoreBundle\Entity\TTableInteraction;
use Autorentool\CoreBundle\Form\DataTransformer\ImgFileToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TItemBodyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tasktype = $options['tasktype'];

        $builder->add('paragraph', TextareaType::class, array(
            'label' => false,
            'attr' => array(
                'class' => 'form-control taskparagraph',
                'placeholder' => 'Aufgabebeschreibung und Aufgabefrage',
            ),
            'required' => true,
        ));

        if ($tasktype == 'single' or $tasktype == 'multiple' or $tasktype == 'table' or $tasktype == 'dragndropTable') {
            $builder->add('removeImgSrcState', CheckboxType::class, array(
                'label' => false,
                'required' => false,
                'attr' => array(
                    'class' => 'itemBody-removeImgSrc',
                    'style' => 'display:none;',
                ),
            ));

            $builder->add('imgSrc', FileType::class, array(
                'label' => false,
                'required' => false,
                'multiple' => false,
                'attr' => array(
                    'class' => 'itemBody imgSrc',
                    'accept' => 'image/*',
                    'style' => 'display:none;',
                ),
            ));

            if ($tasktype == 'single' or $tasktype == 'multiple') {
                $builder->add('choiceInteraction', TChoiceInteractionType::class, array(
                    'label' => false,
                    'tasktype' => $tasktype,
                ));
            } elseif ($tasktype == 'table') {
                $builder->add('tableInteraction', TTableInteractionType::class, array(
                    'label' => false,
                    'tasktype' => $tasktype
                ));
            } elseif ($tasktype == 'dragndropTable') {
                $builder->add('dragInteraction', TDragInteractionType::class, array(
                    'label' => false,
                    'tasktype' => $tasktype,
                ));
            }

        } elseif ($tasktype == 'point') {

            $builder->add('hotspotInteraction', THotspotInteractionType::class, array(
                'label' => false,
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TItemBody::class,
            'tasktype' => null,
            'entity_manager' => null
        ));
    }

    public function getName()
    {
        return 'titembody';
    }
}
<?php

namespace App\Form;

use App\Entity\Comic;
use App\Entity\HotBox;
use App\Enumerations\RotationFrequencyEnumeration;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HotBoxCreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end']
            ])
            ->add('code', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end']
            ])
            ->add('active', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'checkbox-toggle'
                ],
                'label_attr' => [
                    'class' => 'col-form-label checkbox-toggle-icon col-3 text-end'
                ]
            ])
            ->add('rotationFrequency', ChoiceType::class,
                [
                    'choices' => RotationFrequencyEnumeration::getChoices(),
                    'label' => 'Rotation Frequency:',
                    'label_attr' => [
                        'class' => 'col-3 col-form-label text-end'
                    ],
                    'attr' => [
                        'class' => 'form-select'
                    ],
                    'required' => true,
                    'multiple' => false,
                ]
            )
            ->add('imageWidth', NumberType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end']
            ])
            ->add('imageHeight', NumberType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HotBox::class,
        ]);
    }
}

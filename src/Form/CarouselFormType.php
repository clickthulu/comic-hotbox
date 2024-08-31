<?php

namespace App\Form;

use App\Entity\Carousel;
use App\Enumerations\CarouselDisplayTypeEnumeration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarouselFormType extends AbstractType
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
            ->add('width', NumberType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end']
            ])
            ->add('height', NumberType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end']
            ])
            ->add('displayType', ChoiceType::class, [
                'required' => true,
                'choices' => CarouselDisplayTypeEnumeration::getChoices(),
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end'],
                'label' => 'Transition'
            ])
            ->add('delay', NumberType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end'],
                'label' => 'Delay'
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carousel::class,
        ]);
    }
}

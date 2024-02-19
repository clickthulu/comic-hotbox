<?php

namespace App\Form;

use App\Entity\Comic;
use App\Entity\HotBox;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
                'label_attr' => ['class' => 'col-form-label col-2 text-end']
            ])
            ->add('code', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-2 text-end']
            ])
            ->add('active', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'checkbox-toggle'
                ],
                'label_attr' => [
                    'class' => 'col-form-label checkbox-toggle-icon col-2 text-end'
                ]
            ])
            ->add('comics', EntityType::class, [
                'attr' => [
                    'class' => 'form-select'
                ],
                'label_attr' => ['class' => 'col-form-label col-2 text-end'],
                'expanded' => true,
                'required' => false,
                'class' => Comic::class,
                'choice_label' => 'id',
                'multiple' => true,
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

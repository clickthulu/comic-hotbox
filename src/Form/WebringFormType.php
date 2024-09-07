<?php

namespace App\Form;

use App\Entity\Webring;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebringFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $base = __DIR__ . "/../..";
        $webpath = "{$base}/public";
        $navOptions = glob("{$base}/storage/_admin/*");
        $options = [
            'Default' => null,
        ];
        foreach ($navOptions as $ov) {
            $baseov = basename($ov);
            $pathov = str_replace($webpath, '', $ov);
            $options[$baseov] = $pathov;
        }
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
            ->add('numberImages', NumberType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end'],
                'label' => 'Number of Images displayed'
            ])
            ->add('navigationWidth', NumberType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end'],
                'label' => 'Navigation Button Width'
            ])
            ->add('navigationLeft', ChoiceType::class,[
                'multiple' => false,
                'expanded' => false,
                'choices' => $options,
                'attr' => ['class' => 'form-select'],
                'choice_attr' => ['class' => 'col'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end'],
                'label' => 'Navigation Button Image (left)'
            ])
            ->add('navigationRight', ChoiceType::class,[
                'multiple' => false,
                'expanded' => false,
                'choices' => $options,
                'attr' => ['class' => 'form-select'],
                'choice_attr' => ['class' => 'col'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end'],
                'label' => 'Navigation Button Image (right)'
            ])

            ->add('ringWidth', NumberType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end'],
                'label' => 'Total Ring Width'
            ])
            ->add('ringHeight', NumberType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-3 text-end'],
                'label' => 'Ring Height'
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
            'data_class' => Webring::class,
        ]);
    }
}

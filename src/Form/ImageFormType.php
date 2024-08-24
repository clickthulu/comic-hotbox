<?php

namespace App\Form;

use App\Entity\Comic;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alttext', TextareaType::class, [
                'attr' => [
                    'class' => 'form-horizontal form-control',
                    'placeholder' => $options['alttext_placeholder']
                ],
                'label_attr' => ['class' => 'col-form-label col-2 text-end'],
                'label' => 'Image Alt Text',

            ])
            ->add('url', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-horizontal form-control',
                    'placeholder' => $options['url_placeholder']
                ],
                'label_attr' => ['class' => 'col-form-label col-2 text-end'],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'url_placeholder' => '',
            'alttext_placeholder' => ''
        ]);
        $resolver->setAllowedTypes('url_placeholder', ['null','string']);
        $resolver->setAllowedTypes('alttext_placeholder', ['null','string']);
    }
}

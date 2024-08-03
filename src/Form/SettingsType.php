<?php

namespace App\Form;

use App\Entity\SettingsCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('items', CollectionType::class, [
            'entry_type' => SettingsItemType::class,
            'entry_options' => [
                'label' => true,
                'attr' => [
                    'class' => 'row mb-2'
                ]
            ],
            'row_attr' => [
                'class' => 'col'
            ]
        ])
        ->add(
            'submit', SubmitType::class, [
                "attr" => [
                    "class" => 'btn btn-success'
                ],
            ]
        )

        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SettingsCollection::class
        ]);
    }

}
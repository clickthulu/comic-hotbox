<?php

namespace App\Form;

use App\Entity\User;
use App\Enumerations\RoleEnumeration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-2 text-end']
            ])
            ->add('email', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-horizontal form-control'],
                'label_attr' => ['class' => 'col-form-label col-2 text-end']
            ])
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => RoleEnumeration::getChoices(),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => ['class' => 'form-horizontal form-control'],
                    'choice_attr' => ['form-checkbox row'],
                    'label_attr' => ['class' => 'col-form-label col-2 text-end'],
                ],
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

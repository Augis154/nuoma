<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUserFormType extends AbstractType
{
    // private array $role_names = [
    //     'ROLE_USER' => 'Naudotojas',
    //     'ROLE_LENDER' => 'Nuomotojas',
    //     'ROLE_MODERATOR' => 'Kontrolierius',
    //     'ROLE_ADMIN' => 'Administratorius',
    // ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'El. paštas negali būti tuščias',
                    ]),
                ],
            ])
            ->add('username', null, [
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vartotojo vardas negali būti tuščias',
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'choices' => [
                    'Naudotojas' => 'ROLE_USER',
                    'Nuomotojas' => 'ROLE_LENDER',
                    'Kontrolierius' => 'ROLE_MODERATOR',
                    'Administratorius' => 'ROLE_ADMIN',
                ],
                'expanded' => false,
                'multiple' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

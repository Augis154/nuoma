<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class NewItemFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Įveskite pavadinimą',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Pavadinimas turi būti bent {{ limit }} simbolių ilgio',
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('price', MoneyType::class, [
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Įveskite kainą',
                    ]),
                    new Range([
                        'min' => 0,
                        'minMessage' => 'Kaina negali būti neigiama',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'mapped' => true,
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('images', FileType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Įkelkite bent vieną nuotrauką',
                    ]),
                    new Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'Galite įkelti nuo {{ min }} iki {{ max }} nuotraukų',
                        // 'minMessage' => 'Įkelkite bent {{ limit }} nuotrauką',
                        // 'maxMessage' => 'Negalite įkelti daugiau nei {{ limit }} nuotraukų',
                    ]),
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '5M',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                ],
                                'mimeTypesMessage' => 'Priimami tik JPEG ir PNG formato failai',
                            ]),
                        ],
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}

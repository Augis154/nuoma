<?php

namespace App\Form;

use App\Entity\Item;
use App\Enum\ItemCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Range;

class EditItemFormType extends AbstractType
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
            ->add('category', ChoiceType::class, [
                'mapped' => true,
                'required' => true,
                'choices' => [
                    'Darbo įrankiai' => ItemCategory::WORK,
                    'Stalo įrankiai' => ItemCategory::TABLE,
                    'Meno įrankiai' => ItemCategory::ARTS,
                    'Žemės ūkio įrankiai' => ItemCategory::AGRO,
                    'Laisvalaikio įrankiai' => ItemCategory::LEASURE,
                    'Kiti įrankiai' => ItemCategory::OTHER,
                ],
                'expanded' => false,
                'multiple' => false,
                // 'constraints' => [
                //     new Length([
                //         'max' => 4096,
                //     ]),
                // ],
            ])
            ->add('images', FileType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new Count([
                        'max' => 5,
                        'maxMessage' => 'Negalite įkelti daugiau nei {{ limit }} nuotraukų',
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

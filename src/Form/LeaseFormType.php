<?php

namespace App\Form;

use App\Entity\Lease;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class LeaseFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from', DateType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Data negali būti tuščia',
                    ]),
                    // new LessThan([
                    //     'propertyPath' => 'to',
                    //     'message' => 'Pradžios data turi būti ankstesnė už pabaigos datą',
                    // ]),
                    // new LessThan([
                    //     'value' => (new \DateTime())->modify('+1 month'),
                    //     'message' => 'Pradžios data negali būti toliau nei už mėnesį nuo šiandienos',
                    // ]),
                    // new GreaterThan([
                    //     'value' => (new \DateTime())->modify('-1 day'),
                    //     'message' => 'Pradžios data negali būti praeityje',
                    // ]),
                ],
            ])
            ->add('to', DateType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime()->modify('+7 days'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Data negali būti tuščia',
                    ]),
                    // new GreaterThan([
                    //     'propertyPath' => 'from',
                    //     'message' => 'Pabaigos data turi būti vėlesnė už pradžios datą',
                    // ]),
                    // new LessThan([
                    //     'value' => (new \DateTime())->modify('+2 months'),
                    //     'message' => 'Pabaigos data negali būti toliau nei už du mėnesius nuo šiandienos',
                    // ]),
                    // new GreaterThan([
                    //     'value' => (new \DateTime())->modify('-1 day'),
                    //     'message' => 'Pabaigos data negali būti praeityje',
                    // ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lease::class,
        ]);
    }
}

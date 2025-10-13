<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class StationSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alias', TextType::class, [
                'label' => 'Alias',
                'attr' => [
                    'class' => "form-control",
                ],
                'label_attr' => [
                    'class' => 'input-group-text'
                ],
                'row_attr' => [
                'class' => 'input-group mb-2',
            ],
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'Description',
                'attr' => [
                    'class' => "form-control",
                ],
                'label_attr' => [
                    'class' => 'input-group-text'
                ],
                'row_attr' => [
                'class' => 'input-group mb-2',
            ],
            ])
            ->add('latitude', NumberType::class, [
                'required' => false,
                'html5' => true,
                'label' => 'Latitude',
                'attr' => [
                    'class' => "form-control",
                    'step' => 'any',
                    'inputmode' => 'decimal'
                ],
                'label_attr' => [
                    'class' => 'input-group-text'
                ],
                'row_attr' => [
                'class' => 'input-group mb-2',
            ],
                
            ])
            ->add('longitude', NumberType::class, [
                'required' => false,
                'label' => 'Longitude',
                'html5' => true,
                'attr' => [
                    'class' => "form-control",
                    'step' => 'any',
                    'inputmode' => 'decimal'

                ],
                'label_attr' => [
                    'class' => 'input-group-text'
                ],
                'row_attr' => [
                'class' => 'input-group mb-2',
            ],
            ])
            ->add('altitude', NumberType::class, [
                'required' => false,
                'html5' => true,
                'label' => 'Altitude',
                'attr' => [
                    'class' => "form-control",
                    'step' => '1'
                ],
                'label_attr' => [
                    'class' => 'input-group-text'
                ],
                'row_attr' => [
                'class' => 'input-group mb-2',
            ],
            ])
->add('status', CollectionType::class, [
    'entry_type' => CheckboxType::class,
    'entry_options' => [
        'label_attr' => ['class' => 'form-check-label'],
        'attr' => ['class' => 'form-check-input'],
    ],
    'label' => false, // we'll render the label in Twig
    'row_attr' => [
        'class' => 'mb-2',
    ],
]);
   }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

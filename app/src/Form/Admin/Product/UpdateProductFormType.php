<?php

namespace App\Form\Admin\Product;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class UpdateProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'mapped' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a name']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'name must be at least {{ limit }} characters',
                        'maxMessage' => 'name cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_]+$/',
                        'message' => 'name can only contain letters, numbers and underscores',
                    ]),
                ],
            ])
            ->add('count', IntegerType::class, [
                'mapped' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a count']),
                    new Range([
                        'min' => 0,
                        'max' => 4_294_967_295,
                        'notInRangeMessage' => 'count must be at least least 0 and cannot be longer than 4,294,967,295'
                    ])
                ]
            ])
            ->add('price', NumberType::class, [
                'mapped' => true,
                'required' => true,
                'scale' => 2,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a price']),
                    new Range([
                        'min' => 0.01,
                        'max' => 999999.99,
                        'notInRangeMessage' => 'count must be at least least 0.01 and cannot be longer than 999999.99',
                        'invalidMessage' => 'Please enter a valid price',
                    ]),
                    new Type([
                        'type' => 'float',
                        'message' => 'The value {{ value }} is not a valid decimal number.',
                    ])
                ]
            ])
            ->add('categories', EntityType::class, [
                'mapped' => true,
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('isActive', CheckboxType::class, [
                'mapped' => true,
                'required' => false,
                'constraints' => [
                    new Type(['type' => 'bool', 'message' => 'Value must be true или false']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }

}
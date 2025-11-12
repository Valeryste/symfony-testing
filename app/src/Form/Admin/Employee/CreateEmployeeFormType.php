<?php

namespace App\Form\Admin\Employee;

use App\Entity\Employee;
use App\Entity\Shop;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CreateEmployeeFormType extends AbstractType
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
                        'minMessage' => 'name must be at least 3 characters',
                        'maxMessage' => 'name cannot be longer than 255 characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Zа-яА-Я0-9_]+( [a-zA-Zа-яА-Я0-9_]+)*$/u',
                        'message' => 'Name can contain letters, numbers, underscores with single spaces between words',
                    ]),
                ],
            ])
            ->add('surname', TextType::class, [
                'mapped' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a surname']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'surname must be at least 3 characters',
                        'maxMessage' => 'surname cannot be longer than 255 characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Zа-яА-Я0-9_]+( [a-zA-Zа-яА-Я0-9_]+)*$/u',
                        'message' => 'Surname can contain letters, numbers, underscores with single spaces between words',
                    ]),
                ],
            ])
            ->add('position', TextType::class, [
                'mapped' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a position']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'position must be at least 3 characters',
                        'maxMessage' => 'position cannot be longer than 255 characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Zа-яА-Я0-9_]+( [a-zA-Zа-яА-Я0-9_]+)*$/u',
                        'message' => 'Position can contain letters, numbers, underscores with single spaces between words',
                    ]),
                ],
            ])
            ->add('phone', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Phone number is required']),
                    new Length([
                        'min' => 10,
                        'max' => 15,
                        'minMessage' => 'Phone number must be at least {{ limit }} digits',
                    ]),
                    new Regex([
                        'pattern' => '/^\+?[0-9\s\-\(\)]+$/',
                        'message' => 'Phone number can only contain digits, spaces, hyphens and parentheses'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'mapped' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your email']),
                    new Email(['message' => 'Please enter a valid email address']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Email must be at least 3 characters',
                        'maxMessage' => 'Email cannot be longer than 255 characters',
                    ]),
                ],
            ])
            ->add('shop', EntityType::class, [
                'mapped' => true,
                'class' => Shop::class,
                'required' => true,
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
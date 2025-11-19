<?php

namespace App\Form\Admin\User;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class UpdateUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'mapped' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a username']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Username must be at least 3 characters',
                        'maxMessage' => 'Username cannot be longer than 255 characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9]+([_.-]?[a-zA-Z0-9])*$/',
                        'message' => 'Name can only contain Latin letters, numbers, underscores',
                    ]),
                ],
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
            ->add('role', EntityType::class, [
                'mapped' => true,
                'class' => Role::class,
                'choice_label' => 'name'
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
            'data_class' => User::class,
        ]);
    }

}
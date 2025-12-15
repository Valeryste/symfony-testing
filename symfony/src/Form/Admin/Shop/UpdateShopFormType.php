<?php

namespace App\Form\Admin\Shop;

use App\Entity\City;
use App\Entity\Shop;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class UpdateShopFormType extends AbstractType
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
            ->add('city', EntityType::class, [
                'mapped' => true,
                'class' => City::class,
                'required' => true,
                'choice_label' => 'name'
            ])
            ->add('address', TextType::class, [
                'mapped' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a address']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'address must be at least 3 characters',
                        'maxMessage' => 'address cannot be longer than 255 characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9\s\-\.,#]+$/',
                        'message' => 'Address can only contain letters, numbers, spaces, hyphens, commas, periods and hash symbols',
                    ]),
                ],
            ])
            ->add('isOpen', CheckboxType::class, [
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
            'data_class' => Shop::class,
        ]);
    }
}
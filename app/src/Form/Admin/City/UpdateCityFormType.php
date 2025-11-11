<?php

namespace App\Form\Admin\City;

use App\Entity\City;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UpdateCityFormType extends AbstractType
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
                        'pattern' => '/^[a-zA-Zа-яА-Я0-9_\s]+$/u',
                        'message' => 'Name can only contain letters, numbers, underscores and spaces',
                    ]),
                ],
            ])
            ->add('country', EntityType::class, [
                'mapped' => true,
                'class' => Country::class,
                'required' => true,
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => City::class,
        ]);
    }

}
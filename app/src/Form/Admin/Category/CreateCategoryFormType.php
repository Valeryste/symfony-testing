<?php

namespace App\Form\Admin\Category;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CreateCategoryFormType extends AbstractType
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
            ->add('parent', EntityType::class, [
                'mapped' => true,
                'required' => false,
                'class' => Category::class,
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class
        ]);
    }
}
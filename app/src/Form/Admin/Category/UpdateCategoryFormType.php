<?php

namespace App\Form\Admin\Category;

use App\Entity\Category;
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

class UpdateCategoryFormType extends AbstractType
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
                'choices' => $options['availableParents'],
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
            'data_class' => Category::class,
            'availableParents' => [],
        ]);
    }

}
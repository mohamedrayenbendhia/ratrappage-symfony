<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null)
            ->add('email', null)
            ->add('phoneNumber', null)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'mapped' => false,
                'first_options'  => [
                    'label' => 'Password',
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new \Symfony\Component\Validator\Constraints\NotBlank(['message' => 'Password cannot be blank.']),
                        new \Symfony\Component\Validator\Constraints\Length(['min' => 8, 'minMessage' => 'Password must be at least {{ limit }} characters.'])
                    ],
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

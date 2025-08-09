<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'required' => true,
                'empty_data' => '',
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'empty_data' => '',
            ])
            ->add('phoneNumber', null, [
                'required' => true,
                'empty_data' => '',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'mapped' => false,
                'first_options' => [
                    'label' => 'New Password',
                    'attr' => [
                        'placeholder' => 'Leave blank to keep current password',
                        'autocomplete' => 'new-password'
                    ],
                    'constraints' => [
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Password must be at least {{ limit }} characters.',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Repeat New Password',
                    'attr' => [
                        'placeholder' => 'Repeat new password',
                        'autocomplete' => 'new-password'
                    ],
                ],
                'invalid_message' => 'The password fields must match.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

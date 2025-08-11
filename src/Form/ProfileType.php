<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;


class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, GIF)',
                    ])
                ],
                'help' => 'Formats acceptés: JPEG, PNG, GIF. Taille max: 5MB'
            ])
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Name must be at least {{ limit }} characters.',
                        'maxMessage' => 'Name cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control']
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

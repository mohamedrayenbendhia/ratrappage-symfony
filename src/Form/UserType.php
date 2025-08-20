<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email', EmailType::class)
            ->add('phoneNumber')
            ->add('plainPassword', \Symfony\Component\Form\Extension\Core\Type\RepeatedType::class, [
                'type' => \Symfony\Component\Form\Extension\Core\Type\PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'first_options'  => [
                    'label' => 'Password',
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => !$options['is_edit'] ? [
                        new \Symfony\Component\Validator\Constraints\NotBlank(['message' => 'Password cannot be blank.']),
                        new \Symfony\Component\Validator\Constraints\Length([
                            'min' => 8,
                            'minMessage' => 'Password must be at least {{ limit }} characters.',
                        ]),
                    ] : [
                        new \Symfony\Component\Validator\Constraints\Callback(function ($value, $context) {
                            if ($value !== null && $value !== '' && strlen($value) < 8) {
                                $context->buildViolation('Password must be at least 8 characters.')->addViolation();
                            }
                        }),
                    ],
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
            ]);

        // Ajouter le champ de rôles seulement si l'utilisateur connecté est SUPER_ADMIN ou ADMIN
        $user = $this->security->getUser();
        if ($user && (in_array('ROLE_SUPER_ADMIN', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles()))) {
            $roleChoices = [];
            
            if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                // SUPER_ADMIN peut créer tous les rôles
                $roleChoices = [
                    'Client' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                    'Super Admin' => 'ROLE_SUPER_ADMIN',
                ];
            } elseif (in_array('ROLE_ADMIN', $user->getRoles())) {
                // ADMIN peut créer seulement des clients
                $roleChoices = [
                    'Client' => 'ROLE_USER',
                ];
            }

            $builder->add('userRole', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => $roleChoices,
                'mapped' => false,
                'data' => 'ROLE_USER', // Valeur par défaut
                'attr' => ['class' => 'form-control'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}

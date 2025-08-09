<?php

namespace App\Form;

use App\Entity\Rating;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rated', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return $user->getName() . ' (' . $user->getEmail() . ')';
                },
                'label' => 'Rate User',
                'placeholder' => 'Select a user to rate...',
                'choices' => $options['available_users'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('stars', ChoiceType::class, [
                'choices' => [
                    '1 Star' => 1,
                    '2 Stars' => 2,
                    '3 Stars' => 3,
                    '4 Stars' => 4,
                    '5 Stars' => 5,
                ],
                'label' => 'Rating',
                'placeholder' => 'Select rating...',
                'attr' => ['class' => 'form-control']
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Comment (Optional)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Share your experience...'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
            'available_users' => [],
        ]);
    }
}

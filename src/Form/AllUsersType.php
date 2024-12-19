<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllUsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return sprintf('%s %s', $user->getNom(), $user->getPrenom());
                },
                'label' => 'Selectionnez un utilisateur :',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Afficher',
                'attr' => [
                    'class' => 'btn btn-warning',  // Classes CSS du bouton
                    'style' => 'font-size: 16px;', // Styles CSS supplémentaires
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
<?php

namespace App\Form;

use App\Entity\Etat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeEtatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Etat', ChoiceType::class, [
                'choices' => $options['allEtat'],
                'choice_label' => function (Etat $etat) {
                    return $etat->getLibelle();
                },
                'attr'=>[
                    'class'=>'form-control dropdown-toggle'
                ]

            ])
            ->add('submit', SubmitType::class, [
                'label' => 'valider',  // Texte du bouton
                'attr' => [
                    'class' => 'btn btn-success',  // Classes CSS du bouton
                    'style' => 'font-size: 16px;', // Styles CSS supplémentaires
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
        $resolver->setDefined(['allEtat']); // Add this line
    }
}
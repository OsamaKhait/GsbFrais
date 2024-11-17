<?php

namespace App\Form;

use App\Entity\FicheFrais;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheFraisType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('listMois', ChoiceType::class, [
                'label'=>' Selectionnez le mois : ',
                'choices' => $options['data'],
                'data' => new \DateTime('now'),
                'choice_label'=> function($choice): string{
                    $date = \DateTimeImmutable::createFromFormat('Ym',$choice->getMois());
                    return $date->format('F - Y');
                }

            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Voir la fiche',  // Texte du bouton
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
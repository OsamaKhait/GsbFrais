<?php

namespace App\Form;

use App\Entity\Etat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifEtatFicheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etat', ChoiceType::class, [
                'choices' => $options['allEtat'],
                'choice_label'=> function(Etat $etat) {
                    return $etat->getLibelle();
                },
                'attr' => ['class' => 'form-control dropdown-toggle']
            ])
            ->add('Changer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary mt-2 mx-auto d-block']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allEtat' => null,
        ]);
    }
}
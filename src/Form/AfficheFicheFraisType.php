<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AfficheFicheFraisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fichesFrais', ChoiceType::class, [
                'choices' => $options['data'] ?? [], // Handle empty data
                'choice_label' => function ($choice): string {
                    $mois = $choice->getMois();

                    // Check if $mois is a DateTimeImmutable and format it accordingly
                    if ($mois instanceof \DateTimeImmutable) {
                        return $mois->format('m/Y'); // Format to MM/YYYY
                    }

                    // Handle legacy string-based month representation
                    if (is_string($mois) && strlen($mois) >= 4) {
                        $laDate = str_split($mois, 4);
                        return $laDate[0] . '/' . $laDate[1]; // Format it as MM/YYYY
                    }

                    return (string)$mois; // Fallback for unexpected cases
                },
                'placeholder' => 'Sélectionner un mois', // Add a placeholder option
                'required' => true,
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500', // Tailwind CSS for styling
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'mt-4 px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500', // Tailwind CSS for button
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'data' => [], // Ensure we have a fallback for the data
        ]);
    }
}

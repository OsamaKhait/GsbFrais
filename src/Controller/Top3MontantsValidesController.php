<?php

namespace App\Controller;

use App\Repository\FicheFraisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Top3MontantsValidesController extends AbstractController
{
    #[Route('/top3', name: 'app_top3_montants_valides')]
    public function index(Request $request, FicheFraisRepository $ficheFraisRepository): Response
    {
        // Liste des mois de 2024
        $moisList = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre',
        ];

        $selectedMois = $request->request->get('mois');
        $top3FicheFrais = [];

        if ($selectedMois) {
            // Construire la date avec le mois sélectionné
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', '2023-' . $selectedMois . '-01 00:00:00');

            // Formater la date au format YYYYMM
            $formattedDate = $date->format('Ym');

            // Appeler le repository avec la date formatée
            $top3FicheFrais = $ficheFraisRepository->getTop3MontantsValides($formattedDate);
        }

        return $this->render('top3_montants_valides/index.html.twig', [
            'months' => $moisList,
            'selectedMonth' => $selectedMois, // Pass only the selected month key
            'selectedYear' => '2024', // Set the year as a fixed value
            'top3FicheFrais' => $top3FicheFrais,
        ]);
    }
}

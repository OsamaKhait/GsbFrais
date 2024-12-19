<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ComptableController extends AbstractController
{
    #[Route('/comptable', name: 'app_comptable_top3')]
    public function top3Visiteurs(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire pour sélectionner un mois
        $form = $this->createFormBuilder()
            ->add('mois', ChoiceType::class, [
                'choices' => $this->getMoisChoices(),
                'label' => 'Sélectionnez un mois :',
                'placeholder' => 'Choisissez un mois',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Afficher le Top 3',
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->getForm();

        $form->handleRequest($request);

        $topVisiteurs = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $mois = $form->get('mois')->getData();

            // Requête pour obtenir le Top 3 des visiteurs pour le mois sélectionné
            $topVisiteurs = $entityManager->createQuery(
                'SELECT u.nom, u.prenom, SUM(f.montantValid) AS totalMontant
                 FROM App\Entity\FicheFrais f
                 JOIN f.user u
                 WHERE f.mois = :mois
                 GROUP BY u.id
                 ORDER BY totalMontant DESC'
            )
                ->setParameter('mois', $mois)
                ->setMaxResults(3)
                ->getResult();
        }

        return $this->render('comptable/index.html.twig', [
            'form' => $form->createView(),
            'topVisiteurs' => $topVisiteurs,
        ]);
    }

    private function getMoisChoices(): array
    {
        // Génération des mois pour l'année 2024 (format YYYYMM)
        $mois = [];
        for ($i = 1; $i <= 12; $i++) {
            $moisNum = str_pad($i, 2, '0', STR_PAD_LEFT);
            $mois["$moisNum 2024"] = "2024$moisNum";
        }

        return $mois;
    }
}
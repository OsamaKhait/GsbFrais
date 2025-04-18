<?php

namespace App\Controller;

use App\Form\ModifEtatFicheType;
use App\Repository\EtatRepository;
use App\Repository\FicheFraisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FichesFraisUsersController extends AbstractController
{
    #[Route('/fichesfraisusers', name: 'app_fiches_frais_users')]
    public function index(FicheFraisRepository $ficheFraisRepository, EtatRepository $etatRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $changementEtat = false;

        // Récupérer l'ID de la fiche via la query string (meilleure pratique que $_GET)
        $ficheId = $request->query->get('id');
        $ficheFrais = $ficheFraisRepository->find($ficheId);

        // Vérification si la fiche de frais existe
        if (!$ficheFrais) {
            $this->addFlash('danger', 'Fiche de frais introuvable.');
            return $this->redirectToRoute('homepage'); // Redirection si ID invalide
        }

        // Initialisation des totaux à 0 pour éviter les erreurs
        $totalKm = $totalEtape = $totalNuit = $totalRepas = 0;

        // Calcul des totaux en utilisant un switch plus lisible
        foreach ($ficheFrais->getLigneFraisForfaits() as $ligne) {
            switch ($ligne->getFraisForfait()->getId()) {
                case 1:
                    $totalKm = $ligne->getQuantite();
                    break;
                case 2:
                    $totalEtape = $ligne->getQuantite();
                    break;
                case 3:
                    $totalNuit = $ligne->getQuantite();
                    break;
                case 4:
                    $totalRepas = $ligne->getQuantite();
                    break;
            }
        }

        // Création du formulaire de modification d'état
        $formEtat = $this->createForm(ModifEtatFicheType::class, null, [
            'allEtat' => $etatRepository->findAll()
        ]);
        $formEtat->handleRequest($request);

        // Traitement du formulaire soumis
        if ($formEtat->isSubmitted() && $formEtat->isValid()) {
            $selectedEtat = $formEtat->get('etat')->getData();
            $ficheFrais->setEtat($selectedEtat);
            $ficheFrais->setDateModif(new \DateTime());

            $entityManager->persist($ficheFrais);
            $entityManager->flush();

            $this->addFlash('success', 'L’état de la fiche de frais a été modifié avec succès.');
            $changementEtat = true;
        }

        // Rendu de la vue avec les données nécessaires
        return $this->render('fiches_frais_users/index.html.twig', [
            'controller_name' => 'MesFichesController',
            'changeEtat' => $changementEtat,
            'ficheFrais' => $ficheFrais,
            'totalKm' => $totalKm,
            'totalEtape' => $totalEtape,
            'totalNuit' => $totalNuit,
            'totalRepas' => $totalRepas,
            'formEtat' => $formEtat->createView(),
        ]);
    }
}

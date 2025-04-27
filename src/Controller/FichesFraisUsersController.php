<?php

namespace App\Controller;
use App\Form\ModifEtatType;
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
        $selectedFiche = $ficheFraisRepository->find($_GET['id']);
        foreach ( $selectedFiche->getLigneFraisForfaits() as $ligne){
            if($ligne->getFraisForfait()->getId() == 1){
                $totalKm = $ligne->getQuantite();
            } elseif ($ligne->getFraisForfait()->getId() == 2){
                $totalEtape = $ligne->getQuantite();
            } elseif ($ligne->getFraisForfait()->getId() == 3){
                $totalNuit = $ligne->getQuantite();
            } else {
                $totalRepas = $ligne->getQuantite();
            }
        }

        $formEtat = $this->createForm(ModifEtatType::class, $selectedFiche, [
            'allEtat' => $etatRepository->findAll()
        ]);
        $formEtat->handleRequest($request);

        if($formEtat->isSubmitted() && $formEtat->isValid()) {
            $selectedEtat = $formEtat->get('etat')->getData();
            $selectedFiche->setEtat($selectedEtat);
            $selectedFiche->setDateModif(new \DateTimeImmutable());
            $entityManager->persist($selectedFiche);
            $entityManager->flush();
            $changementEtat = true;

            return $this->render('fiches_frais_users/index.html.twig', [
                'controller_name' => 'GestionComptableController',
                'changeEtat' => $changementEtat,
                'ficheDate' => $selectedFiche,
                'totalKm' => $totalKm,
                'totalEtape' => $totalEtape,
                'totalNuit' => $totalNuit,
                'totalRepas' => $totalRepas,
                'formEtat' => $formEtat->createView(),
            ]);

        }

        return $this->render('fiches_frais_users/index.html.twig', [
            'controller_name' => 'MesFichesController',
            'ficheDate' => $selectedFiche,
            'totalKm' => $totalKm,
            'totalEtape' => $totalEtape,
            'totalNuit' => $totalNuit,
            'totalRepas' => $totalRepas,
            'formEtat' => $formEtat->createView(),
        ]);
    }
}

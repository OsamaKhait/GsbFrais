<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use App\Form\LigneFraisForfaitType;
use App\Form\LigneFraisHorsForfaitType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SaisirFicheFraisController extends AbstractController
{
    #[Route('/saisirfiche', name: 'app_saisir_fiche_frais')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $mois = new \DateTime('now');
        $mois -> modify('first day of this month');
        $mois = \DateTimeImmutable::createFromMutable($mois);
        $ficheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy(['user' => $user, 'mois' => $mois]);

        if ($ficheFrais == null) {

            $ficheFrais = new FicheFrais();

            $ficheFrais->setMois($mois);
            $ficheFrais->setUser($user);
            $ficheFrais->setDateModif(new \DateTime('now'));
            $ficheFrais->setNbJustificatifs(0);
            $ficheFrais->setMontantValid(0);
            $ficheFrais->setEtat($entityManager->getRepository(Etat::class)->findOneBy(['id' => 1]));

            $fraisForfaitRepository = $entityManager->getRepository(FraisForfait::class);
            $fraisEtape = $fraisForfaitRepository->find(1);
            $fraisKm = $fraisForfaitRepository->find(2);
            $fraisNuitee = $fraisForfaitRepository->find(3);
            $fraisRepas = $fraisForfaitRepository->find(4);


            $ligneFraisForfaitEtape = new LigneFraisForfait();
            $ligneFraisForfaitEtape->setFicheFrais($ficheFrais);
            $ligneFraisForfaitEtape->setFraisForfait($fraisEtape);
            $ligneFraisForfaitEtape->setQuantite(0);
            $entityManager->persist($ligneFraisForfaitEtape);

            $ligneFraisForfaitKm = new LigneFraisForfait();
            $ligneFraisForfaitKm->setFicheFrais($ficheFrais);
            $ligneFraisForfaitKm->setFraisForfait($fraisKm);
            $ligneFraisForfaitKm->setQuantite(0);
            $entityManager->persist($ligneFraisForfaitKm);

            $ligneFraisForfaitNuitee = new LigneFraisForfait();
            $ligneFraisForfaitNuitee->setFicheFrais($ficheFrais);
            $ligneFraisForfaitNuitee->setFraisForfait($fraisNuitee);
            $ligneFraisForfaitNuitee->setQuantite(0);
            $entityManager->persist($ligneFraisForfaitNuitee);

            $ligneFraisForfaitRepas = new LigneFraisForfait();
            $ligneFraisForfaitRepas->setFicheFrais($ficheFrais);
            $ligneFraisForfaitRepas->setFraisForfait($fraisRepas);
            $ligneFraisForfaitRepas->setQuantite(0);
            $entityManager->persist($ligneFraisForfaitRepas);

            $ficheFrais->addLigneFraisForfait($ligneFraisForfaitEtape);
            $ficheFrais->addLigneFraisForfait($ligneFraisForfaitKm);
            $ficheFrais->addLigneFraisForfait($ligneFraisForfaitNuitee);
            $ficheFrais->addLigneFraisForfait($ligneFraisForfaitRepas);

            $entityManager->persist($ficheFrais);
            $entityManager->flush();
        }

        foreach ($ficheFrais->getLigneFraisForfait() as $ligneFraisForfait) {
            $fraisForfait = $ligneFraisForfait->getFraisForfait();
            if ($fraisForfait !== null) {
                $id = $fraisForfait->getId();
                if ($id == 1) {
                    $ligneFraisForfaitEtape = $ligneFraisForfait;
                }
                if ($id == 2) {
                    $ligneFraisForfaitKm = $ligneFraisForfait;
                }
                if ($id == 3) {
                    $ligneFraisForfaitNuitee = $ligneFraisForfait;
                }
                if ($id == 4) {
                    $ligneFraisForfaitRepas = $ligneFraisForfait;
                }
            }
        }

        $formForfait = $this->createForm(LigneFraisForfaitType::class);

        // Handle form submissions
        $formForfait->handleRequest($request);

        if ($formForfait->isSubmitted() && $formForfait->isValid()) {
            // Get the data from the form
            $data = $formForfait->getData();

            // Set the data to the LigneFraisForfait entities
            $ligneFraisForfaitEtape->setQuantite($data['ForfaitEtape']);
            $ligneFraisForfaitKm->setQuantite($data['FraisKilometrique']);
            $ligneFraisForfaitNuitee->setQuantite($data['NuiteeHotel']);
            $ligneFraisForfaitRepas->setQuantite($data['RepasRestaurant']);

            // Persist the entities
            $entityManager->persist($ligneFraisForfaitEtape);
            $entityManager->persist($ligneFraisForfaitKm);
            $entityManager->persist($ligneFraisForfaitNuitee);
            $entityManager->persist($ligneFraisForfaitRepas);

            // Flush the changes
            $entityManager->flush();

        }
        else {
            $formForfait->get('ForfaitEtape')->setData($ligneFraisForfaitEtape->getQuantite());
            $formForfait->get('FraisKilometrique')->setData($ligneFraisForfaitKm->getQuantite());
            $formForfait->get('NuiteeHotel')->setData($ligneFraisForfaitNuitee->getQuantite());
            $formForfait->get('RepasRestaurant')->setData($ligneFraisForfaitRepas->getQuantite());
        }
        $lfhf = new LigneFraisHorsForfait();
        $formHorsForfait = $this->createForm(LigneFraisHorsForfaitType::class, $lfhf);


// Handle form submissions
        $formHorsForfait->handleRequest($request);

        if ($formHorsForfait->isSubmitted() && $formHorsForfait->isValid()) {
            // Get the data from the form

            $lfhf->setFicheFrais($ficheFrais);
            $entityManager->persist($lfhf);
            $entityManager->flush();
        }
        $affichageLigneFHF = $entityManager->getRepository(LigneFraisHorsForfait::class)->findBy(['ficheFrais' => $ficheFrais]);

        return $this->render('saisir_fiche_frais/index.html.twig', [
            'formFraisForfait' => $formForfait ? $formForfait->createView() : null,
            'formHorsForfait' => $formHorsForfait ? $formHorsForfait->createView() : null,
            'fraisHorsForfaitAffichage' => $affichageLigneFHF
        ]);


    }
    public function delete(Request $request, LigneFraisHorsForfait $fraisHorsForfait): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($fraisHorsForfait);
        $entityManager->flush();

        return $this->redirectToRoute('app_saisir_fiche_frais');
    }
}
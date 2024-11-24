<?php


namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\User;
use App\Form\AfficheFicheFraisType;
use App\Repository\EtatRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AfficherFichesController extends AbstractController
{
    #[Route('/mesfiches', name: 'app_afficher_fiches')]
    public function index(Request $request, ManagerRegistry $doctrine, EtatRepository $etatRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $myUser = $doctrine->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()]);
        $fichesfrais = $myUser->getFicheFrais();

        $form = $this->createForm(AfficheFicheFraisType::class, $fichesfrais);
        $form->handleRequest($request);
        $selectedFiche=null;
        $lignefraisforfait= null;
        if ($form->isSubmitted()&& $form->isValid()){
            /** @var FicheFrais $selectedFiche */
            $selectedFiche = $form->get('fichesFrais')->getData();

            $lignefraisforfait = $form->get('fichesFrais')->getData();
        }



        return $this->render('afficher_fiches/index.html.twig', [
            'controller_name' => 'AfficherFichesController',
            'form' => $form->createView(),
            'selectedFiche'=> $selectedFiche,
        ]);
    }
}



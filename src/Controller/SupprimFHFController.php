<?php

namespace App\Controller;

use App\Entity\LigneFraisHorsForfait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupprimFHFController extends AbstractController
{
    #[Route('/delete/fiche/hors/forfait/{id}', name: 'app_delete_fiche_hors_forfait')]
    public function index($id, EntityManagerInterface $entityManager): Response
    {
        $ficheHorForfait = $entityManager->getRepository(LigneFraisHorsForfait::class)->find($id);

        if($ficheHorForfait != null){
            $entityManager->remove($ficheHorForfait);
            $entityManager->flush();
        }
        return $this->render('supprim_fhf/index.html.twig', [
        ]);
    }
}
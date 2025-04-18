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
    public function index(LigneFraisHorsForfait $ficheHorsForfait, EntityManagerInterface $entityManager): Response
    {
        // Use type-hinting to automatically handle 404 if the entity doesn't exist
        try {
            $entityManager->remove($ficheHorsForfait);
            $entityManager->flush();

            // Add a flash message for user feedback
            $this->addFlash('success', 'La ligne de frais hors forfait a été supprimée avec succès.');
        } catch (\Exception $e) {
            // Add an error flash message if something goes wrong
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de la ligne de frais.');
        }

        // Redirect back to the expense entry page
        return $this->redirectToRoute('app_saisir_fiche_frais');
    }
}
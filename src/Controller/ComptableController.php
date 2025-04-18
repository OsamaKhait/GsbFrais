<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComptableController extends AbstractController
{
    #[Route('/comptable', name: 'app_comptable')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $users = $doctrine->getRepository(User::class)->findAll();
        $lesUsers = [];
        foreach ($users as $user) {
            $lesUsers[$user->getNom()] = $user->getId();
        }

        $ficheUsers = null;
        $form = $this->createFormBuilder()
            ->add('user', ChoiceType::class, [
                'choices'  => $lesUsers,
                'label' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ficheUsers = $doctrine->getRepository(FicheFrais::class)->findBy(['user' => $form->get('user')->getData()]);
        }

        return $this->render('comptable/index.html.twig', [
            'controller_name' => 'ComptableController',
            'form' => $form->createView(),
            'selectedFiche' => $ficheUsers,
        ]);
    }
}
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedirectionControlController extends AbstractController
{
    #[Route('/', name: 'app_redirection_control')]
    public function index(): Response

    {
        return $this->render('redirection_control/index.html.twig', [
            'controller_name' => 'RedirectionControlController',
        ]);
    }
}

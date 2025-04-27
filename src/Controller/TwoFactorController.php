<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TwoFactorController extends AbstractController
{
    #[Route('/2fa', name: '2fa_login')]
    public function twoFactorForm(): Response
    {
        return $this->render('security/2fa_form.html.twig');
    }

    #[Route('/2fa/setup', name: '2fa_setup')]
    public function setup2FA(GoogleAuthenticatorInterface $googleAuthenticator, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isTotpAuthenticationEnabled()) {
            $secret = $googleAuthenticator->generateSecret();
            $user->setTotpSecret($secret);
            $user->enableTotpAuthentication();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $qrCodeUrl = $googleAuthenticator->getQRContent($user);

        return $this->render('security/2fa_setup.html.twig', [
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }
}

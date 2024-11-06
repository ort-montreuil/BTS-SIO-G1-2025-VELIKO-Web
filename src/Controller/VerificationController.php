<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class VerificationController extends AbstractController
{
    #[Route('/verification/{token}', name: 'app_verification')]
    public function index(string $token, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Le lien de confirmation est invalide ou a expiré.');
            return $this->redirectToRoute('app_login');
        }

        $user->setVerified(1); // Change ici à 1 pour indiquer que l'utilisateur est vérifié
        $user->setConfirmationToken(null); // Supprime le token de confirmation
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été confirmé. Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('app_login');
    }
}
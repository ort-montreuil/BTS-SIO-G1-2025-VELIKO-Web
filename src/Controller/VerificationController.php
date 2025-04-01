<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

// Confirmation de l'utilisateur si lien mail ok
class VerificationController extends AbstractController
{
    #[Route('/verification/{token}', name: 'app_verification')]
    public function index(string $token, EntityManagerInterface $entityManager): Response
    {
        // Récupère l'utilisateur avec le token de confirmation
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
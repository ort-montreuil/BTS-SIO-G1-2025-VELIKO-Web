<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Si l'utilisateur est déjà connecté, vérifier s'il est vérifié
        if ($this->getUser() instanceof User) {
            if (!$this->getUser()->isVerified()) {
                $this->addFlash('error', 'Veuillez confirmer votre compte avant de vous connecter.');
                return $this->redirectToRoute('app_logout'); // Redirige vers la déconnexion pour s'assurer qu'il se déconnecte
            }
            return $this->redirectToRoute('app_home'); // Redirige vers la page d'accueil si vérifié
        }



        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/check', name: 'app_login_check')]
    public function check(): Response
    {
        return new Response(); // Retourne une réponse vide
    }
}
<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

//connexion user
//Gere la connexion et la déconnexion des utilisateurs et verifie si l'utilisateur est vérifié ou bloqué avant de lui permettre de se connecter

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

            // Si l'utilisateur est connecté mais pas vérifié, on lui demande de vérifier son email
            if (!$this->getUser()->isVerified()) {
                $this->addFlash('error', 'Veuillez confirmer votre compte avant de vous connecter.');
                // Redirige vers la page d'accueil ou vers une page d'information spécifique
                return $this->redirectToRoute('app_home');
            }
            if ($this->getUser()->isVerified() && $this->getUser()->isBloqued()) {
                $this->addFlash('error', 'Votre compte a été bloqué. Veuillez contacter l\'administrateur.');
                return $this->redirectToRoute('app_blocked');
            }



            /////////////////////////////////////////////////////////////
           /* // Vérifier si le mot de passe a été forcé
            if ($this->getUser()->isForcedmdp()) {
                $this->addFlash('warning', 'Votre mot de passe a été forcé. Veuillez le changer pour continuer à vous connecter.');
                return $this->redirectToRoute('app_user_modif_mdp'); // Redirige vers la page pr remplacer le mot de passe
            }*/


            // Si l'utilisateur est vérifié, redirige vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }

        // Si l'utilisateur n'est pas connecté, afficher le formulaire de connexion
        return $this->render('security/login.html.twig', [
            // le nom de l'utilisateur saisi précédemment ds inscription
            'last_username' => $lastUsername,
            // le message d'erreur de connexion
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
        return new Response(); //c'est symfony qui s'occupe de la réponse
    }
}

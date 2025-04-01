<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

// Edition du profil utilisateur
class EditController extends AbstractController
{

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/edit/{id}', name: 'app_edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        // Vérifier si l'utilisateur est connecté et s'il est autorisé à modifier le profil
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur est autorisé à modifier le profil
        if ($this->getUser()->getId() !== $user->getId()) {
            return $this->redirectToRoute('app_home');
        }

        // Créer le formulaire de modification de profil
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et s'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
           //Verif si modif si oui modif ds la bdd
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger l'utilisateur vers la page d'accueil + message
            $this->addFlash('success', 'User updated successfully!');
            return $this->redirectToRoute('app_home');
        }
        // Afficher le formulaire de modification de profil
        return $this->render('edit/index.html.twig', [
            'controller_name' => 'EditController',
            'form' => $form->createView(),
            'user' => $user,

        ]);

    }

    #[Route('/delete/{id}', name: 'app_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        // Vérifier si l'utilisateur est connecté et s'il est autorisé à supprimer le profil
        if (!$this->getUser() || $this->getUser()->getId() !== $user->getId()) {
            return $this->redirectToRoute('app_login');
        }

        // Anonymiser l'utilisateur
        $user->setNom('Anonyme');
        $user->setPrenom('Anonyme');

        // Générer une adresse e-mail anonyme unique
        $emailAnonyme = 'anonyme_' . $user->getId() . '@example.com';
        $user->setEmail($emailAnonyme);

        // Hacher le mot de passe
        $hashedPassword = bin2hex(random_bytes(10));
        $user->setPassword($hashedPassword);

        // Enregistrer les modifications dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // Déconnexion de l'utilisateur
        $this->tokenStorage->setToken(null);
        $this->addFlash('success', 'Votre compte a été anonymisé avec succès. Vous avez été déconnecté.');

        return $this->redirectToRoute('app_home');
    }
}
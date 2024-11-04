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
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser()->getId() !== $user->getId()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully!');

            return $this->redirectToRoute('app_home');
        }
        return $this->render('edit/index.html.twig', [
            'controller_name' => 'EditController',
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
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

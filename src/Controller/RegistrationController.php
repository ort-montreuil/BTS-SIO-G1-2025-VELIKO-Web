<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Email;
use App\GenerateToken\GenerateToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et s'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Générer un token de confirmation pour l'utilisateur
            $token = GenerateToken::generateNewToken(32);
            $user->setConfirmationToken($token);

            $entityManager->persist($user);
            $entityManager->flush();

            $confirmationUrl = $this->generateUrl('app_verification', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new Email())
                ->from('no-reply@tonsite.com')
                ->to($user->getEmail())
                ->subject('Confirmation de votre inscription')
                ->html('<p>Merci pour votre inscription ! Cliquez sur le lien pour confirmer votre email : <a href="' . $confirmationUrl . '">Confirmer mon email</a></p>');

            $mailer->send($email);

            return $this->redirectToRoute('app_home', [
                'message' => 'Un email de confirmation a été envoyé. Vérifiez votre boîte de réception.'
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    public function confirmEmail(string $token, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Token de confirmation invalide.');
            return $this->redirectToRoute('app_home');
        }

        // Confirmer l'utilisateur
        $user->setVerified(1); // Mets à jour la vérification
        $user->setConfirmationToken(null);

        $entityManager->flush();

        // Connecte l'utilisateur seulement si la confirmation a réussi
        $this->loginUser($user);

        $this->addFlash('success', 'Votre compte a été confirmé avec succès !');
        return $this->redirectToRoute('app_home');
    }




}
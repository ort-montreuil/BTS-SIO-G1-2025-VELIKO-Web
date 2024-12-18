<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\PasswordResetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MotsDePasseController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PasswordResetType::class);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et s'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                $user->generatePasswordResetToken();
                $entityManager->flush();

                $resetLink = $this->generateUrl('app_reset_password', [
                    'token' => $user->getPasswordResetToken()
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                if ($user->isForcedMdp()){
                    $emailMessage = (new Email())
                        ->from('noreply@example.com')
                        ->to($user->getEmail())
                        ->subject('Renouvellement de mot de passe')
                        ->html("Cliquez sur le lien suivant pour renouveler votre mot de passe : <a href='$resetLink'>Renouveler le mot de passe</a>");
                    $mailer->send($emailMessage);
                }else{

                $emailMessage = (new Email())
                    ->from('noreply@example.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de mot de passe')
                    ->html("Cliquez sur le lien suivant pour réinitialiser votre mot de passe : <a href='$resetLink'>Réinitialiser le mot de passe</a>");

                $mailer->send($emailMessage);
                }
            }


            $this->addFlash('success', 'Un email de modification de mot de passe vous a été envoyé.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('mots_de_passe/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(
        Request $request,
        string $token,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Récupérer l'utilisateur avec le token de réinitialisation de mot de passe
        $user = $entityManager->getRepository(User::class)->findOneBy(['passwordResetToken' => $token]);

        // Vérifier si l'utilisateur existe et si le token est valide
        if (!$user || $user->getPasswordResetTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('error', 'Le lien de réinitialisation est invalide ou expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et s'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $user->setPasswordResetToken(null);
            $user->setPasswordResetTokenExpiresAt(null);
            $entityManager->flush();

            if ($user->isForcedMdp()){
                $user->setForcedMdp(false);
                $entityManager->flush();
                $this->addFlash('success', 'Votre mot de passe a été renouvellé.');
                return $this->redirectToRoute('app_login');
            }else{

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé.');
            return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('mots_de_passe/passwordReset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
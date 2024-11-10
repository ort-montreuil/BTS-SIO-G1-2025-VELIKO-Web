<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class UserModifMdpController extends AbstractController
{
    #[Route('/user/modif/mdp', name: 'app_user_modif_mdp')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createFormBuilder()
            ->add('currentPassword', PasswordType::class, ['label' => 'Ancien mot de passe'])
            ->add('newPassword', PasswordType::class, ['label' => 'Nouveau mot de passe'])
            ->add('confirmPassword', PasswordType::class, ['label' => 'Confirmer le mot de passe'])
            ->add('submit', SubmitType::class, ['label' => 'Modifier le mot de passe'])
            ->getForm();

        // Gérer la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (!$passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
                $this->addFlash('error', 'L\'ancien mot de passe est incorrect.');
                return $this->redirectToRoute('app_user_modif_mdp');
            }

            if ($data['newPassword'] !== $data['confirmPassword']) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_user_modif_mdp');
            }

            $user->setPassword($passwordHasher->hashPassword($user, $data['newPassword']));

            // Utilisation de l'EntityManagerInterface pour persister les modifications
            $entityManager->flush();

            $this->addFlash('success', 'Le mot de passe a été modifié avec succès.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('user_modif_mdp/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

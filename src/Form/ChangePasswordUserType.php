<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
//pr oublie de mdp non connecté
class ChangePasswordUserType

{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Créer le formulaire de changement de mot de passe
        $builder
            ->add('currentPassword', PasswordType::class, ['label' => 'Ancien mot de passe'])
            ->add('newPassword', PasswordType::class, ['label' => 'Nouveau mot de passe'])
            ->add('confirmPassword', PasswordType::class, ['label' => 'Confirmer le mot de passe'])
            ->add('submit', SubmitType::class, ['label' => 'Modifier le mot de passe']);
    }

}
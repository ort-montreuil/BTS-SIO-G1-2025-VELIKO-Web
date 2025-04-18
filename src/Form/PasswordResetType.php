<?php

// src/Form/PasswordResetType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
//modif mdp oublié via mail
class PasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Créer le formulaire de réinitialisation du mot de passe
        $builder

            ->add('email', EmailType::class)
            ->add('submit', SubmitType::class, ['label' => 'Envoyer']);
    }
}

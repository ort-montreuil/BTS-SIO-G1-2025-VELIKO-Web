<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException(
                'Votre compte est inactif ! Impossible de se connecter'
            );
        }
        if ($user->isBloqued()) {
            throw new CustomUserMessageAuthenticationException(
                'Votre compte est bloqué ! Impossible de se connecter'
            );
        }
        if($user->isForcedmdp()){
            throw new CustomUserMessageAuthenticationException(
                'Le changement de votre mot de passe a été forcé ! Veuillez le changer pour pouvoir vous connecter.'
            );
        }
    }
    public function checkPostAuth(UserInterface $user): void
    {
        $this->checkPreAuth($user);
    }
}
<?php
namespace App\Listener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class LoginListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            InteractiveLoginEvent::class => 'onSecurityInteractiveLogin',
        ];
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user->isVerified()) {
            throw new AccessDeniedException('Vous devez confirmer votre compte avant de vous connecter.');
        }
    }
}

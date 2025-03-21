<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getEnabled()) { 
            throw new CustomUserMessageAccountStatusException('Disabled account.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Tu peux ajouter d'autres vérifications après l'authentification ici si nécessaire
    }
}

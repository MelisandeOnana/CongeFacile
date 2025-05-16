<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileService
{
    public function updatePerson(EntityManagerInterface $entityManager, Person $person): bool
    {
        try {
            $entityManager->persist($person);
            $entityManager->flush();
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function handlePasswordReset($form, User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): string
    {
        $currentPassword = $form->get('currentPassword')->getData();
        $newPassword = $form->get('newPassword')->getData();
        $confirmPassword = $form->get('confirmPassword')->getData();

        if (! $passwordHasher->isPasswordValid($user, $currentPassword)) {
            return 'Le mot de passe actuel est incorrect.';
        }
        if ($newPassword === $currentPassword) {
            return 'Le nouveau mot de passe doit être différent de l\'ancien.';
        }
        if ($newPassword !== $confirmPassword) {
            return 'Les mots de passe ne correspondent pas.';
        }

        try {
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $entityManager->persist($user);
            $entityManager->flush();
            return 'success';
        } catch (\Exception) {
            return 'Une erreur est survenue lors de la réinitialisation de votre mot de passe.';
        }
    }
}
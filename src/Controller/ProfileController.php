<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PreferencesType;
use App\Form\ProfileType;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile_index')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, PersonRepository $personRepository): Response
    {
        $user = $this->getUser();
        if (! $user instanceof User) {
            throw new \Exception('L\'utilisateur n\'est pas connecté.');
        }
        $person = $personRepository->find($user->getPerson()->getId());
        $isManager = $this->isGranted('ROLE_MANAGER');

        $form = $this->createForm(ProfileType::class, $person, [
            'department' => $person->getDepartment(),
            'is_manager' => $isManager,
        ]);
        $form->get('email')->setData($user->getEmail());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updatePerson($entityManager, $person);
            return $this->redirectToRoute('profile_index');
        }

        $resetPasswordForm = $this->createForm(ResetPasswordType::class);
        $resetPasswordForm->handleRequest($request);

        if ($resetPasswordForm->isSubmitted() && $resetPasswordForm->isValid()) {
            if ($this->handlePasswordReset($resetPasswordForm, $user, $passwordHasher, $entityManager)) {
                return $this->redirectToRoute('profile_index');
            }
        }

        return $this->render('profile/profile.html.twig', [
            'form' => $form->createView(),
            'resetPasswordForm' => $resetPasswordForm->createView(),
            'isManager' => $isManager,
        ]);
    }

    private function updatePerson(EntityManagerInterface $entityManager, $person): void
    {
        try {
            $entityManager->persist($person);
            $entityManager->flush();
            $this->addFlash('success', 'Vos informations ont été mises à jour.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de vos informations.');
        }
    }

    private function handlePasswordReset($form, $user, $passwordHasher, $entityManager): bool
    {
        $currentPassword = $form->get('currentPassword')->getData();
        $newPassword = $form->get('newPassword')->getData();
        $confirmPassword = $form->get('confirmPassword')->getData();

        if (! $passwordHasher->isPasswordValid($user, $currentPassword)) {
            $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
            return false;
        }
        if ($newPassword === $currentPassword) {
            $this->addFlash('error', 'Le nouveau mot de passe doit être différent de l\'ancien.');
            return false;
        }
        if ($newPassword !== $confirmPassword) {
            $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            return false;
        }

        try {
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé.');
            return true;
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la réinitialisation de votre mot de passe.');
            return false;
        }
    }

    #[Route('/preferences', name: 'preferences')]
    public function preferences(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PreferencesType::class);
        $user = $this->getUser();
        if (! $user instanceof User) {
            throw new \Exception('L\'utilisateur n\'est pas connecté.');
        }
        $person = $user->getPerson();

        $form->setData($person);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $entityManager->persist($user);
                $entityManager->flush();
                // Mettre à jour les préférences de l'utilisateur


                $this->addFlash('success', 'Vos préférences ont été mises à jour.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de vos préférences.');

            }

            return $this->redirectToRoute('preferences');
        }

        return $this->render('profile/preferences.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

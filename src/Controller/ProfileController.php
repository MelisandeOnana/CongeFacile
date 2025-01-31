<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\ProfileType;
use App\Form\PreferencesType;
use App\Form\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile_index')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        $person = $user->getPerson();

        // Forcer le chargement de l'entité
        $person = $entityManager->getRepository(Person::class)->find($person->getId());

        // Créer le formulaire et passer les données de la personne
        $form = $this->createForm(ProfileType::class, $person, [
            'department' => $person->getDepartment(),
        ]);

        // Définir la valeur du champ email
        $form->get('email')->setData($user->getEmail());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications
            $entityManager->persist($person);
            $entityManager->flush();

            // Ajouter un message flash ou rediriger l'utilisateur
            $this->addFlash('success', 'Vos informations ont été mises à jour.');
            return $this->redirectToRoute('profile_index');
        }

        // Créer le formulaire de réinitialisation du mot de passe
        $resetPasswordForm = $this->createForm(ResetPasswordType::class);

        $resetPasswordForm->handleRequest($request);
        if ($resetPasswordForm->isSubmitted() && $resetPasswordForm->isValid()) {
            // Vérifier le mot de passe actuel
            $currentPassword = $resetPasswordForm->get('currentPassword')->getData();
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
            } else {
                // Réinitialiser le mot de passe
                $newPassword = $resetPasswordForm->get('plainPassword')->getData();
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                // Ajouter un message flash ou rediriger l'utilisateur
                $this->addFlash('success', 'Votre mot de passe a été réinitialisé.');
                return $this->redirectToRoute('profile_index');
            }
        }

        return $this->render('default/profile/index.html.twig', [
            'form' => $form->createView(),
            'resetPasswordForm' => $resetPasswordForm->createView(),
        ]);
    }
    #[Route('/preferences', name: 'preferences')]
    public function preferences(): Response
    {
        $form = $this->createForm(PreferencesType::class);

        return $this->render('default/profile/preferences.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
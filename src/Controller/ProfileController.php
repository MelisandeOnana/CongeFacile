<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\User;
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
        if (!$user instanceof User) {
            throw new \Exception('L\'utilisateur n\'est pas connecté.');
        }
        $person = $user->getPerson();

        // Forcer le chargement de l'entité
        $person = $entityManager->getRepository(Person::class)->find($person->getId());

        // Déterminer si l'utilisateur est un manager
        $isManager = $this->isGranted('ROLE_MANAGER');

        // Créer le formulaire et passer les données de la personne
        $form = $this->createForm(ProfileType::class, $person, [
            'department' => $person->getDepartment(),
            'is_manager' => $isManager,
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
                $newPassword = $resetPasswordForm->get('newPassword')->getData();
                $confirmPassword = $resetPasswordForm->get('confirmPassword')->getData();
                if ($newPassword !== $confirmPassword) {
                    $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                } else {
                    $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                    $user->setPassword($hashedPassword);

                    $entityManager->persist($user);
                    $entityManager->flush();

                    // Ajouter un message flash et rediriger
                    $this->addFlash('success', 'Votre mot de passe a été réinitialisé.');
                    return $this->redirectToRoute('profile_index');
                }
            }
        }

        // Rediriger vers la vue appropriée en fonction du rôle
        if ($isManager) {
            return $this->render('default/profile/manager/manager.html.twig', [
                'form' => $form->createView(),
                'resetPasswordForm' => $resetPasswordForm->createView(),
            ]);
        } else {
            return $this->render('default/profile/collaborator/collaborator.html.twig', [
                'form' => $form->createView(),
                'resetPasswordForm' => $resetPasswordForm->createView(),
            ]);
        }
    }

    #[Route('/preferences', name: 'preferences')]
    public function preferences(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PreferencesType::class);
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \Exception('L\'utilisateur n\'est pas connecté.');
        }
        $person = $user->getPerson();
        $roles = $user->getRoles();
        $alertNewRequest = $person->getAlertNewRequest();
        $alertOnAnswer = $person->getAlertOnAnswer();
        $alertBeforeVacation = $person->getAlertBeforeVacation();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (in_array("ROLE_MANAGER", $roles)) {
                $person->setAlertNewRequest($form->get('alertNewRequest')->getData());
            } else {
                $person->setAlertOnAnswer($form->get('alertOnAnswer')->getData());
                $person->setAlertBeforeVacation($form->get('alertBeforeVacation')->getData());
            }
            
            $entityManager->persist($person);
            $entityManager->flush();

            $this->addFlash('success', 'Vos préférences ont été mises à jour.');
            return $this->redirectToRoute('preferences');
        }

        return $this->render('default/profile/preferences.html.twig', [
            'form' => $form->createView(),
            'alertNewRequest' => $alertNewRequest,
            'alertOnAnswer' => $alertOnAnswer,
            'alertBeforeVacation' => $alertBeforeVacation,
        ]);
    }
}
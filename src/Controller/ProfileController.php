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
use App\Service\ProfileService;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile_index')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        PersonRepository $personRepository,
        ProfileService $profileService
    ): Response
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
            if ($profileService->updatePerson($entityManager, $person)) {
                $this->addFlash('success', 'Vos informations ont été mises à jour.');
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de vos informations.');
            }
            return $this->redirectToRoute('profile_index');
        }

        $resetPasswordForm = $this->createForm(ResetPasswordType::class);
        $resetPasswordForm->handleRequest($request);

        if ($resetPasswordForm->isSubmitted() && $resetPasswordForm->isValid()) {
            $result = $profileService->handlePasswordReset($resetPasswordForm, $user, $passwordHasher, $entityManager);
            if ($result === 'success') {
                $this->addFlash('success', 'Votre mot de passe a été réinitialisé.');
                return $this->redirectToRoute('profile_index');
            } else {
                $this->addFlash('error', $result);
            }
        }

        return $this->render('profile/profile.html.twig', [
            'form' => $form->createView(),
            'resetPasswordForm' => $resetPasswordForm->createView(),
            'isManager' => $isManager,
        ]);
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

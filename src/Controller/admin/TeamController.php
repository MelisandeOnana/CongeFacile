<?php

namespace App\Controller\admin;

use App\Entity\Person;
use App\Entity\Position;
use App\Entity\User;
use App\Form\DeleteType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\TeamMemberSearchType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Department;

#[IsGranted('ROLE_MANAGER')]
class TeamController extends AbstractController
{
    #[Route('/team', name: 'team_index')]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $userManager = $this->getUser();
        if (!$userManager instanceof User) {
            return $this->redirectToRoute('login');
        }

        $personManager = $userManager->getPerson();
        $department = $personManager->getDepartment();

        $form = $this->createForm(TeamMemberSearchType::class);
        $form->handleRequest($request);

        $criteria = $form->isSubmitted() && $form->isValid() ? $form->getData() : [];

        // Récupérer les membres de l'équipe
        $query = $userRepository->findTeamMembersQuery($criteria, $personManager, $department);

        // Ajouter la pagination
        $teamMembers = $paginator->paginate(
            $query, // Query ou tableau
            $request->query->getInt('page', 1), // Numéro de la page
            10 // Nombre d'éléments par page
        );

        // Calculer les jours de congé pour chaque membre
        foreach ($teamMembers as $member) {
            $totalVacationDays = $userRepository->getVacationDaysForYear($member, (int) date('Y'));
            $member->totalVacationDays = $totalVacationDays; // Ajouter dynamiquement une propriété
        }

        return $this->render('admin/team/index.html.twig', [
            'form' => $form->createView(),
            'teamMembers' => $teamMembers, // Conserver l'objet de pagination
        ]);
    }

    #[Route('/team/new', name: 'team_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userManager = $this->getUser();
        if (! $userManager instanceof User) {
            return $this->redirectToRoute('login');
        }
        $personManager = $userManager->getPerson();

        $person = new Person();
        $user = new User();
        $user->setPerson($person);

        $user->setManager($personManager);
        $user->getPerson()->setDepartment($personManager->getDepartment());

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // Définir des valeurs par défaut pour les champs requis
            $person->setAlertOnAnswer(false);
            $person->setAlertNewRequest(false);
            $person->setAlertBeforeVacation(false);

            // Définir une valeur par défaut pour le champ position_id
            $position = $userForm->get('position')->getData();
            if ($position) {
                $person->setPosition($position);
            } else {
                // Définir une valeur par défaut si nécessaire
                $defaultPosition = $entityManager->getRepository(Position::class)->find(1); // Récupérer ou définir une valeur par défaut
                $person->setPosition($defaultPosition);
            }

            // Définir une valeur par défaut pour le champ enabled
            $user->setEnabled(true);

            // Définir une valeur par défaut pour le champ created_at
            $user->setCreatedAt(new \DateTimeImmutable());

            // Hash the password
            $newPassword = $userForm->get('newPassword')->getData();
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $newPassword
                );
                $user->setPassword($hashedPassword);
            }

            $user->setManager($personManager);
            $user->getPerson()->setDepartment($personManager->getDepartment());
            $user->setPerson($person);
            $user->setRole('ROLE_COLLABORATOR');

            $entityManager->persist($person); // Persister d'abord la personne
            try {
                $entityManager->flush();
                // Ajouter un message flash
                $this->addFlash('success', 'Le nouveau membre a été ajouté avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du nouveau membre.');
            }

            return $this->redirectToRoute('team_index');
        }

        return $this->render('admin/team/collaborator_new.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }

    #[Route('/team/details/{id}', name: 'team_details')]
    public function memberUpdate(Request $request, int $id, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userManager = $this->getUser();
        if (! $userManager instanceof User) {
            return $this->redirectToRoute('login');
        }
        $personManager = $userManager->getPerson();

        $user = $userRepository->find($id);

        // Vérifier si l'utilisateur existe
        if (! $user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $person = $user->getPerson();

        // Créer le formulaire avec l'utilisateur récupéré
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);
        $delete = $request->query->get('delete');
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ('true' == $delete) {
            if ($formDelete->isSubmitted() && $formDelete->isValid()) {
                $entityManager->remove($person);
                $entityManager->remove($user);
                $entityManager->flush();

                return $this->redirectToRoute('team_index');
            }
        }

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // Définir une valeur par défaut pour le champ position_id
            $position = $userForm->get('position')->getData();
            if ($position) {
                $person->setPosition($position);
            } else {
                // Définir une valeur par défaut si nécessaire
                $defaultPosition = $entityManager->getRepository(Position::class)->find(1); // Récupérer ou définir une valeur par défaut
                $person->setPosition($defaultPosition);
            }

            // Définir une valeur par défaut pour le champ enabled
            $user->setEnabled($userForm->get('enabled')->getData());

            // Hash the password
            $newPassword = $userForm->get('newPassword')->getData();
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $newPassword
                );
                $user->setPassword($hashedPassword);
            }

            $user->setManager($personManager);
            $user->getPerson()->setDepartment($personManager->getDepartment());
            $user->setPerson($person);
            $entityManager->persist($person); // Persister d'abord la personne
            $entityManager->persist($user);   // Puis persister l'utilisateur

            try {
                $entityManager->flush();
                // Ajouter un message flash
                $this->addFlash('success', 'Le membre a été mis à jour avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour du membre.');
            }

            return $this->redirectToRoute('team_index');
        }

        return $this->render('admin/team/collaborator_show.html.twig', [
            'userForm' => $userForm->createView(),
            'member' => $person,
            'user' => $user,
            'formDelete' => $formDelete,
        ]);
    }
}

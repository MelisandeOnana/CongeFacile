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
use App\Repository\PersonRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

#[IsGranted('ROLE_MANAGER')]
class TeamController extends AbstractController
{
    #[Route('/team', name: 'team_index')]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        // Vérifie si l'utilisateur a le rôle de manager
        $userManager = $this->getUser();
        if (!$userManager instanceof User) {
            return $this->redirectToRoute('login');
        }

        // Récupération de la personne et du département du manager
        $personManager = $userManager->getPerson();
        $department = $personManager->getDepartment();

        // Vérifie si le manager a un département
        $form = $this->createForm(TeamMemberSearchType::class);
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        $criteria = $form->isSubmitted() && $form->isValid() ? $form->getData() : [];

        // Récupération des membres de l'équipe
        $query = $userRepository->findTeamMembersQuery($criteria, $userManager, $department);

        // // Pagination : 10 collaborateurs par page
        $teamMembers = $paginator->paginate(
            $query, // Query ou tableau
            $request->query->getInt('page', 1), // Numéro de la page
            10 // Nombre d'éléments par page
        );

        // Calcule les jours de congé pour chaque membre
        foreach ($teamMembers as $member) {
            $totalVacationDays = $userRepository->getVacationDaysForYear($member, (int) date('Y'));
            $member->totalVacationDays = $totalVacationDays; // Ajoute dynamiquement une propriété
        }


        // Filtrer les membres de l'équipe en fonction du critère 'GetVacationDay'
        if (isset($criteria['totalVacationDays'])) {
            $FilterTotalVacationDays = $criteria['totalVacationDays'];
            $filteredMembers = [];
            foreach ($teamMembers as $member) {
                if ($member->totalVacationDays == $FilterTotalVacationDays) {
                    $filteredMembers[] = $member;
                }
            }
            $teamMembers = $paginator->paginate(
                $filteredMembers,
                $request->query->getInt('page', 1),
                10
            );
            
    
        }

        return $this->render('admin/team/index.html.twig', [
            'form' => $form->createView(),
            'teamMembers' => $teamMembers, // Conservation de l'objet de pagination
        ]);
    }

    #[Route('/team/new', name: 'team_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, PersonRepository $personRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle de manager
        $userManager = $this->getUser();
        if (! $userManager instanceof User) {
            return $this->redirectToRoute('login');
        }

        // Récupération de la personne et du département du manager
        $person = new Person();
        $user = new User();
        $user->setEnabled(false); // Par défaut, le profil n'est pas activé
        $user->setCreatedAt(new \DateTimeImmutable()); // Date actuelle
        $user->setPerson($person);

        // Crée le formulaire avec l'utilisateur récupéré
        $userForm = $this->createForm(UserType::class, $user, [
            'include_enabled' => true, // Inclure le champ "enabled"
            'require_password' => true,
        ]);
        // Vérifie si l'utilisateur a un département
        $userForm->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // Vérifie si l'email existe déjà
            $email = $userForm->get('email')->getData();
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($existingUser) {
                $this->addFlash('error', 'Un utilisateur avec cet email existe déjà.');
                return $this->redirectToRoute('team_new');
            }

            // Défini des valeurs par défaut pour les champs requis
            $person->setAlertOnAnswer(false);
            $person->setAlertNewRequest(false);
            $person->setAlertBeforeVacation(false);

            // Défini une valeur par défaut pour le champ position_id
            $position = $userForm->get('position')->getData();
            // Vérifie si une position est sélectionnée
            if ($position) {
                $person->setPosition($position);
            } else {
                // Si aucune position n'est sélectionnée, définir une valeur par défaut
                $defaultPosition = $entityManager->getRepository(Position::class)->find(1);
                $person->setPosition($defaultPosition);
            }

            // Défini une valeur par défaut pour le champ enabled
            $user->setEnabled(true);

            // Défini une valeur par défaut pour le champ created_at
            $user->setCreatedAt(new \DateTimeImmutable());

            // Récupère le département sélectionné
            $department = $userForm->get('department')->getData();
            if ($department) {
                // Récupère le manager associé au département
                $manager = $personRepository->findOneBy([
                    'department' => $department,
                    'manager' => null, // Trouve une personne qui est un manager (relation ManyToOne)
                ]);

                // Vérifie si un manager est trouvé
                if ($manager) {
                    $person->setManager($manager);
                }
            }

            // Hashage du mot de passe
            $newPassword = $userForm->get('newPassword')->getData();
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $newPassword
                );
                $user->setPassword($hashedPassword);
            }

            // Défini le rôle de l'utilisateur
            $user->setPerson($person);
            $user->setRole('ROLE_COLLABORATOR');

            // Vérifie si l'email existe déjà
            $entityManager->persist($person);
            $entityManager->persist($user);

            try {
                $entityManager->flush();
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
        // Vérifie si l'utilisateur a le rôle de manager
        $userManager = $this->getUser();
        if (! $userManager instanceof User) {
            return $this->redirectToRoute('login');
        }
        // Récupération de la personne et du département du manager
        $personManager = $userManager->getPerson();
        // Vérifie si le manager a un département
        $user = $userRepository->find($id);

        // Vérifie si l'utilisateur existe
        if (! $user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Vérifie si l'utilisateur appartient au même département que le manager
        $person = $user->getPerson();

        // Création du formulaire avec l'utilisateur récupéré
        $userForm = $this->createForm(UserType::class, $user, [
            'include_enabled' => true, // Inclure le champ "enabled"
            'require_password' => false, // Ne pas exiger de mot de passe pour la mise à jour
        ]);
        // Vérifie si l'utilisateur a un département
        $userForm->handleRequest($request);
        $delete = $request->query->get('delete');
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        // Vérifie si le formulaire de suppression a été soumis
        if ('true' == $delete) {
            if ($formDelete->isSubmitted() && $formDelete->isValid()) {
                // Supprimer l'utilisateur et la personne
                $entityManager->remove($person);
                $entityManager->remove($user);
                $entityManager->flush();

                // Ajouter un message flash de succès
                $this->addFlash('success', 'Le membre a été supprimé avec succès.');

                return $this->redirectToRoute('team_index');
            }
        }

        // Vérifie si le formulaire a été soumis et est valide
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // Mise à jour de la position
            $position = $userForm->get('position')->getData();
            if ($position) {
                $person->setPosition($position);
            } else {
                // Si aucune position n'est sélectionnée, définir une valeur par défaut
                $defaultPosition = $entityManager->getRepository(Position::class)->find(1);
                $person->setPosition($defaultPosition);
            }

            // Mise à jour du champ enabled
            $isEnabled = $userForm->get('enabled')->getData();
            $user->setEnabled($isEnabled);

            // Si le profil est réactivé, mettre à jour la colonne updated_at
            if ($isEnabled) {
                $user->setUpdatedAt(new \DateTimeImmutable());
            }

            // Hashage du mot de passe si un nouveau mot de passe est fourni
            $newPassword = $userForm->get('newPassword')->getData();
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $newPassword
                );
                $user->setPassword($hashedPassword);
            }

            // Persister les modifications
            $user->setPerson($person);
            $entityManager->persist($person);
            $entityManager->persist($user);

            // Enregistrer les modifications dans la base de données
            try {
                $entityManager->flush();
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

    #[Route('/managers/by-department', name: 'managers_by_department')]
    public function getManagersByDepartment(Request $request, PersonRepository $userRepository): JsonResponse
    {
        $departmentId = $request->query->get('department');

        // Vérifie si le département est fourni
        if (!$departmentId) {
            return new JsonResponse([], 400);
        }

        // Récupération des managers par département
        $managers = $userRepository->findManagerByDepartmentId($departmentId);

        // Vérifie si des managers sont trouvés
        $data = [];

        // Si aucun manager n'est trouvé, renvoie une réponse vide
        foreach ($managers as $manager) {
            $data[] = [
                'id' => $manager->getId(),
                'name' => $manager->getFirstname() . ' ' . $manager->getLastname(),
            ];
        }

        return new JsonResponse($data);
    }
}

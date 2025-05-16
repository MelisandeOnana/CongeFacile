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
use App\Service\TeamService;

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
    public function new(Request $request, TeamService $teamService, PersonRepository $personRepository): Response
    {
        $userManager = $this->getUser();
        if (! $userManager instanceof User) {
            return $this->redirectToRoute('login');
        }

        $person = new Person();
        $user = new User();
        $user->setEnabled(false);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setPerson($person);

        $userForm = $this->createForm(UserType::class, $user, [
            'include_enabled' => true,
            'require_password' => true,
        ]);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $email = $userForm->get('email')->getData();
            if (!$teamService->isEmailUnique($email)) {
                $this->addFlash('error', 'Un utilisateur avec cet email existe déjà.');
                return $this->redirectToRoute('team_new');
            }

            $teamService->setDefaultAlerts($person);

            $position = $userForm->get('position')->getData();
            $department = $userForm->get('department')->getData();

            if ($position && $position->getName() === 'Manager' && $department) {
                if (!$teamService->isManagerUnique($department, $position)) {
                    $this->addFlash('error', 'Il y a déjà un manager pour ce département.');
                    return $this->redirectToRoute('team_new');
                }
            }

            $teamService->setUserRole($user, $position, $department);
            $teamService->setDefaultPosition($person, $position);
            $user->setEnabled(true);
            $user->setCreatedAt(new \DateTimeImmutable());
            $teamService->setManager($person, $department);

            $newPassword = $userForm->get('newPassword')->getData();
            $teamService->hashPassword($user, $newPassword);

            try {
                $teamService->saveUserAndPerson($user, $person);
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
    public function memberUpdate(Request $request, int $id, UserRepository $userRepository, TeamService $teamService): Response
    {
        $userManager = $this->getUser();
        if (! $userManager instanceof User) {
            return $this->redirectToRoute('login');
        }
        $personManager = $userManager->getPerson();
        $user = $userRepository->find($id);

        if (! $user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $person = $user->getPerson();
        if (
            !$person ||
            !$personManager ||
            !$person->getDepartment() ||
            !$personManager->getDepartment() ||
            $person->getDepartment()->getId() !== $personManager->getDepartment()->getId()
        ) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que les membres de votre équipe.');
        }

        $userForm = $this->createForm(UserType::class, $user, [
            'include_enabled' => true,
            'require_password' => false,
        ]);
        $userForm->handleRequest($request);
        $delete = $request->query->get('delete');
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ('true' == $delete) {
            if ($formDelete->isSubmitted() && $formDelete->isValid()) {
                $teamService->removeUserAndPerson($user, $person);
                $this->addFlash('success', 'Le membre a été supprimé avec succès.');
                return $this->redirectToRoute('team_index');
            }
        }

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $position = $userForm->get('position')->getData();
            $teamService->setDefaultPosition($person, $position);

            $isEnabled = $userForm->get('enabled')->getData();
            $user->setEnabled($isEnabled);

            if ($isEnabled) {
                $user->setUpdatedAt(new \DateTimeImmutable());
            }

            $newPassword = $userForm->get('newPassword')->getData();
            $teamService->hashPassword($user, $newPassword);

            try {
                $teamService->saveUserAndPerson($user, $person);
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

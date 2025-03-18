<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Entity\Person;
use App\Entity\Position;
use App\Form\DeleteType;
use App\Form\UserInformationsType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Exception;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MANAGER')]
class TeamController extends AbstractController
{
    #[Route('/team', name: 'team_index')]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new Exception('L\'utilisateur n\'est pas connecté.');
        }

        $manager = $user->getPerson();
        $department = $manager->getDepartment(); // Récupérer le département du manager

        $queryBuilder = $userRepository->createQueryBuilder('user')
            ->join('user.person', 'person')
            ->where('person.manager = :manager')
            ->andWhere('person.department = :department') // Filtrer par département
            ->setParameter('manager', $manager)
            ->setParameter('department', $department);

        // Ajout des filtres
        $filters = [
            'lastName' => $request->query->get('filter_lastName'),
            'firstName' => $request->query->get('filter_firstName'),
            'email' => $request->query->get('filter_email'),
            'position' => $request->query->get('filter_position'),
            'vacationDays' => $request->query->get('filter_vacationDays'),
        ];

        foreach ($filters as $key => $value) {
            if ($value) {
                $queryBuilder->andWhere("person.$key LIKE :$key")
                             ->setParameter($key, '%' . $value . '%');
            }
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        $currentYear = (int) date('Y');
        $vacationDays = [];
        foreach ($pagination as $user) {
            $vacationDays[$user->getId()] = $userRepository->getVacationDaysForYear($user, $currentYear);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('default/admin/team/_table.html.twig', [
                'pagination' => $pagination,
                'vacationDays' => $vacationDays,
            ]);
        }

        return $this->render('default/admin/team/index.html.twig', [
            'pagination' => $pagination,
            'vacationDays' => $vacationDays,
        ]);
    }

    #[Route('/team/new', name: 'team_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $person = new Person();
        $user = new User();
        $user->setPerson($person); // Associer la personne à l'utilisateur

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
        
            // Définir le département de la personne
            $department = $userForm->get('department')->getData();
            if ($department) {
                $person->setDepartment($department);
            } else {
                // Ajouter un message de débogage si le département n'est pas trouvé
                $this->addFlash('error', 'Le département sélectionné n\'a pas été trouvé.');
            }
        
            // Définir le rôle et le manager en fonction de la position
            
                $user->setRole('ROLE_COLLABORATOR');
                // Trouver le manager du département et l'affilier
                $departmentManager = $entityManager->getRepository(Person::class)->findOneBy([
                    'department' => $person->getDepartment()
                    , 'manager' => null
                ]);
                $person->setManager($departmentManager);
            
        
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
        
            $entityManager->persist($person); // Persister d'abord la personne
            $entityManager->persist($user);   // Puis persister l'utilisateur
        
            $entityManager->flush();
            // Ajouter un message flash
            $this->addFlash('success', 'Le nouveau membre a été ajouté avec succès.');
        
            return $this->redirectToRoute('team_index');
        }

        return $this->render('default/admin/team/new_collaborator.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }

    #[Route('/team/details/{id}', name: 'memberInformations')]
    public function memberUpdate(Request $request, int $id, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $userRepository->find($id);

        // Vérifier si l'utilisateur existe
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $person = $user->getPerson();

        // Créer le formulaire avec l'utilisateur récupéré
        $userForm = $this->createForm(UserInformationsType::class, $user);
        $userForm->handleRequest($request);
        $delete = $request->query->get('delete');
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);


        if ($delete == 'true') {
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
        
            // Définir le département de la personne
            $department = $userForm->get('department')->getData();
            if ($department) {
                $person->setDepartment($department);
            } else {
                // Ajouter un message de débogage si le département n'est pas trouvé
                $this->addFlash('error', 'Le département sélectionné n\'a pas été trouvé.');
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
        
            $entityManager->persist($person); // Persister d'abord la personne
            $entityManager->persist($user);   // Puis persister l'utilisateur
        
            $entityManager->flush();
            // Ajouter un message flash
            $this->addFlash('success', 'Le nouveau membre a été ajouté avec succès.');
        
            return $this->redirectToRoute('team_index');
        }

        return $this->render('default/admin/team/memberInformations.html.twig', [
            'userForm' => $userForm->createView(),
            'member' => $person,
            'user' => $user,
        ]);
    }
}
<?php

namespace App\Controller\admin;

use App\Entity\Person;
use App\Entity\User;
use App\Form\ManagerType;
use App\Repository\DepartmentRepository;
use App\Repository\PersonRepository;
use App\Repository\PositionRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MANAGER')]
class ManagerController extends AbstractController
{
    #[Route('/manager', name: 'managers')]
    public function index(DepartmentRepository $departmentRepository, PersonRepository $personRepository, PaginatorInterface $paginator, HttpRequest $request): Response
    {
        $departments = $departmentRepository->findAll();

        // Récupérer les valeurs des filtres depuis la requête
        $filterFirstName = $request->query->get('firstname');
        $filterLastName = $request->query->get('lastname');
        $filterDepartment = $request->query->get('department');

        $criteria = Criteria::create();

        $criteria->andWhere(Criteria::expr()->isNull('manager'));

        if ($filterFirstName) {
            $criteria->andWhere(Criteria::expr()->contains('firstName', $filterFirstName));
        }
        if ($filterLastName) {
            $criteria->andWhere(Criteria::expr()->contains('lastName', $filterLastName));
        }
        if ($filterDepartment) {
            $department = $departmentRepository->find(intval($filterDepartment));

            if ($department) {
                $criteria->andWhere(Criteria::expr()->eq('department', $department));
            } else {
                $this->addFlash('error', 'Le département sélectionné n\'existe pas.');
            }
        }

        $criteria->orderBy(['id' => 'DESC']);
        $filteredManagers = $personRepository->matching($criteria);

        $ManagersPagination = $paginator->paginate(
            $filteredManagers, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            6 /* limit par page */
        );

        return $this->render('admin/manager/index.html.twig', [
            'managers' => $ManagersPagination,
            'departments' => $departments,
        ]);
    }

    #[Route('/manager/details/{id}', name: 'manager_show')]
    public function manager_show(HttpRequest $request, int $id, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, PersonRepository $personRepository, UserRepository $userRepository, DepartmentRepository $departmentRepository): Response
    {
        $criteria = Criteria::create();
        $departmentIsallowed = true;

        $manager = $userRepository->find($id);

        // Vérifier si l'utilisateur existe
        if (! $manager) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $userForm = $this->createForm(ManagerType::class, $manager);

        $criteria->andWhere(Criteria::expr()->isNull('manager'));
        $criteria->andWhere(Criteria::expr()->neq('id', $manager->getId()));
        $managers = $personRepository->matching($criteria);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // Définir le département de la personne
            $department = $userForm->get('department')->getData();

            foreach ($managers as $theManager) {
                if ($theManager->getDepartment()->getName() == $department->getName()) {
                    $this->addFlash('error', 'Le département sélectionné est deja attribué');
                    $departmentIsallowed = false;
                }
            }
            if ($department && $departmentIsallowed) {
                $manager->getPerson()->setDepartment($department);
            } else {
                $this->addFlash('error', 'Le département sélectionné n\'a pas été trouvé.');
            }

            // Hash the password
            $newPassword = $userForm->get('newPassword')->getData();
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $manager,
                    $newPassword
                );
                $manager->setPassword($hashedPassword);
            }

            $entityManager->persist($manager->getPerson()); // Persister d'abord la personne
            $entityManager->persist($manager);   // Puis persister l'utilisateur

            try {
                $entityManager->flush();
                // Ajouter un message flash
                $this->addFlash('success', 'Le manager a été mis à jour avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour du manager.');
            }

            return $this->redirectToRoute('managers');
        }

        return $this->render('admin/manager/manager_show.html.twig', [
            'manager' => $manager,
            'userForm' => $userForm,
        ]);
    }

    #[Route('/manager/new', name: 'manager_new')]
    public function new(HttpRequest $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, PersonRepository $personRepository, DepartmentRepository $departmentRepository, PositionRepository $positionRepository): Response
    {
        $criteria = Criteria::create();
        $departmentIsallowed = true;

        $manager = new User();
        $person = new Person();
        $manager->setPerson($person);

        $userForm = $this->createForm(ManagerType::class, $manager);

        $criteria->andWhere(Criteria::expr()->isNull('manager'));
        $criteria->andWhere(Criteria::expr()->neq('id', $manager->getId()));
        $managers = $personRepository->matching($criteria);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // Définir le département de la personne
            $department = $userForm->get('department')->getData();

            // Vérifier si le département est déjà attribué
            $existingManager = $personRepository->findOneBy(['department' => $department]);
            if ($existingManager) {
                $this->addFlash('error', 'Le département sélectionné est déjà attribué.');

                return $this->redirectToRoute('manager_new'); // Rediriger vers la page de création avec le message d'erreur
            }

            // Définir la position par défaut pour un manager
            $managerPosition = $positionRepository->findOneBy(['name' => 'Manager']);
            if ($managerPosition) {
                $manager->setPosition($managerPosition);
            } else {
                $userForm->get('position')->addError(new FormError('La position "manager" n\'existe pas.'));
            }

            // Hash the password
            $newPassword = $userForm->get('newPassword')->getData();
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $manager,
                    $newPassword
                );
                $manager->setPassword($hashedPassword);
            }

            $entityManager->persist($manager->getPerson()); // Persister d'abord la personne
            $entityManager->persist($manager);   // Puis persister l'utilisateur

            try {
                $entityManager->flush();
                // Ajouter un message flash
                $this->addFlash('success', 'Le manager a été ajouté avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du manager.');
            }

            return $this->redirectToRoute('managers');
        }

        return $this->render('admin/manager/manager_new.html.twig', [
            'userForm' => $userForm,
        ]);
    }
}

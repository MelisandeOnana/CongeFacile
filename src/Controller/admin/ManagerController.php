<?php

namespace App\Controller\admin;

use App\Entity\Person;
use App\Entity\User;
use App\Form\ManagerSearchType;
use App\Form\ManagerType;
use App\Repository\DepartmentRepository;
use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use App\Service\ManagerService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Exception;

#[IsGranted('ROLE_MANAGER')]
class ManagerController extends AbstractController
{
    #[Route('/manager', name: 'managers')]
    public function index(
        DepartmentRepository $departmentRepository,
        PersonRepository $personRepository,
        PaginatorInterface $paginator,
        HttpRequest $request
    ): Response
    {
        $departments = $departmentRepository->findAll();
        $form = $this->createForm(ManagerSearchType::class);
        $form->handleRequest($request);

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
            $filteredManagers,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/manager/index.html.twig', [
            'managers' => $ManagersPagination,
            'departments' => $departments,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/manager/details/{id}', name: 'manager_show')]
    public function manager_show(
        HttpRequest $request,
        int $id,
        UserRepository $userRepository,
        PersonRepository $personRepository,
        DepartmentRepository $departmentRepository,
        ManagerService $managerService
    ): Response
    {
        $criteria = Criteria::create();
        $manager = $userRepository->find($id);

        if (! $manager) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $userForm = $this->createForm(ManagerType::class, $manager, ['require_password' => false]);
        $criteria->andWhere(Criteria::expr()->isNull('manager'));
        $criteria->andWhere(Criteria::expr()->neq('id', $manager->getId()));
        $managers = $personRepository->matching($criteria);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $department = $userForm->get('department')->getData();

            // Vérifier si le département est déjà attribué à un autre manager
            $isAvailable = true;
            foreach ($managers as $theManager) {
                if ($theManager->getDepartment()->getName() == $department->getName()) {
                    $this->addFlash('error', 'Le département sélectionné est deja attribué');
                    $isAvailable = false;
                }
            }

            if ($department && $isAvailable) {
                $managerService->assignDepartment($manager, $department);
            } else {
                $this->addFlash('error', 'Le département sélectionné n\'a pas été trouvé.');
            }

            // Hash du mot de passe si besoin
            $managerService->hashPassword($manager, $userForm->get('newPassword')->getData());

            try {
                $managerService->saveManager($manager);
                $this->addFlash('success', 'Le manager a été mis à jour avec succès.');
            } catch (\Exception $e) {
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
    public function new(
        HttpRequest $request,
        ManagerService $managerService
    ): Response
    {
        $manager = new User();
        $person = new Person();
        $manager->setPerson($person);

        $userForm = $this->createForm(ManagerType::class, $manager);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $department = $userForm->get('department')->getData();

            if (!$managerService->isDepartmentAvailable($department)) {
                $this->addFlash('error', 'Le département sélectionné est déjà attribué.');
                return $this->render('admin/manager/manager_new.html.twig', [
                    'userForm' => $userForm,
                    'manager'=> $manager,
                    'person'=>$person
                ]);
            }

            $managerService->assignManagerPosition($manager);
            $managerService->assignDepartment($manager, $department);
            $managerService->hashPassword($manager, $userForm->get('newPassword')->getData());

            try {
                $managerService->saveManager($manager);
                $this->addFlash('success', 'Le manager a été ajouté avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du manager.');
            }

            return $this->redirectToRoute('managers');
        }

        return $this->render('admin/manager/manager_new.html.twig', [
            'userForm' => $userForm,
            'manager'=> $manager,
            'person'=>$person
        ]);
    }
}

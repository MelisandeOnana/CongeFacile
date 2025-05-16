<?php

namespace App\Controller\admin;

use App\Repository\DepartmentRepository;
use App\Entity\Department;
use App\Form\DepartmentType;
use App\Form\DepartmentSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\DeleteType;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\DepartmentService;


class DepartmentController extends AbstractController
{
    #[Route('/departments', name: 'departments')]
    public function index(Request $request, PaginatorInterface $paginator, DepartmentService $departmentService): Response
    {
        $form = $this->createForm(DepartmentSearchType::class);
        $form->handleRequest($request);

        $search = $form->get('search')->getData() ?? '';

        // Utilisation du service pour la recherche et la pagination
        $departments = $departmentService->getPaginatedDepartments($search, $paginator, $request);

        return $this->render('admin/department/index.html.twig', [
            'departments' => $departments,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/departments/new', name: 'department_new')]
    public function new(Request $request, DepartmentService $departmentService): Response
    {
        $department = new Department();
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$departmentService->createDepartment($department)) {
                $this->addFlash('error', 'Un département avec ce nom existe déjà.');
                return $this->redirectToRoute('department_new');
            }
            $this->addFlash('success', 'Le département a été créé avec succès.');
            return $this->redirectToRoute('departments');
        }

        return $this->render('admin/department/department_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/departments/edit/{id}', name: 'department_edit')]
    public function edit($id, HttpRequest $request, DepartmentService $departmentService): Response
    {
        $department = $departmentService->findDepartment($id);
        if (! $department) {
            throw $this->createNotFoundException('Le département n\'existe pas.');
        }

        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            if (!$departmentService->canDeleteDepartment($department)) {
                $this->addFlash('error', 'Impossible de supprimer ce département car il est associé à des collaborateurs.');
                return $this->redirectToRoute('department_edit', ['id' => $id]);
            }
            try {
                $departmentService->deleteDepartment($department);
                $this->addFlash('success', 'Le département a été supprimé avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression du département.');
            }
            return $this->redirectToRoute('departments');
        }

        $formDepartment = $this->createForm(DepartmentType::class, $department);
        $formDepartment->handleRequest($request);

        if ($formDepartment->isSubmitted() && $formDepartment->isValid()) {
            if (!$departmentService->updateDepartment($department)) {
                $this->addFlash('error', 'Un département avec ce nom existe déjà.');
                return $this->redirectToRoute('department_edit', ['id' => $id]);
            }
            $this->addFlash('success', 'Le département a été mis à jour avec succès.');
            return $this->redirectToRoute('departments');
        }

        return $this->render('admin/department/department_edit.html.twig', [
            'department' => $department,
            'formDepartment' => $formDepartment->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }
}
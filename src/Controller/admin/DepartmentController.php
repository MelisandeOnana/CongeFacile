<?php

namespace App\Controller\admin;

use App\Repository\DepartmentRepository;
use App\Entity\Department;
use App\Form\DepartmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\DeleteType;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Exception;


class DepartmentController extends AbstractController
{
    #[Route('/departments', name: 'departments')]
    public function index(Request $request, DepartmentRepository $departmentRepository): Response
    {
        $name = $request->query->get('name', ''); // Récupère la valeur du champ de recherche
        $departments = $name 
            ? $departmentRepository->findByName($name) 
            : $departmentRepository->findAllOrderedByNewest(); // Utilise la méthode triée

        return $this->render('admin/department/index.html.twig', [
            'departments' => $departments,
        ]);
    }

    #[Route('/departments/new', name: 'department_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $department = new Department();
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($department);
            $entityManager->flush();

            $this->addFlash('success', 'Le département a été créé avec succès.');

            return $this->redirectToRoute('departments');
        }

        return $this->render('admin/department/department_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/departments/edit/{id}', name: 'department_edit')]
    public function edit(DepartmentRepository $departmentRepository, $id, HttpRequest $request, EntityManagerInterface $entityManager): Response
    {
        $department = $departmentRepository->find($id);
    
        if (! $department) {
            throw $this->createNotFoundException('Le département n\'existe pas.');
        }
    
        $formDepartment = $this->createForm(DepartmentType::class, $department);
        $formDepartment->handleRequest($request);
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);
    
        // Vérifiez si le formulaire de suppression a été soumis
        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            try {
                $entityManager->remove($department);
                $entityManager->flush();
                $this->addFlash('success', 'Le département a été supprimé avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression du département.');
            }
    
            return $this->redirectToRoute('departments');
        }
    
        // Vérifiez si le formulaire d'édition a été soumis
        if ($formDepartment->isSubmitted() && $formDepartment->isValid()) {
            $existingDepartment = $departmentRepository->findOneBy(['name' => $department->getName()]);
            if ($existingDepartment && $existingDepartment->getId() !== $department->getId()) {
                $this->addFlash('error', 'Un département avec ce nom existe déjà.');
    
                return $this->redirectToRoute('department_edit', ['id' => $id]);
            } else {
                $entityManager->persist($department);
                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'Le département a été mis à jour avec succès.');
                } catch (Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour du département.');
                }
    
                return $this->redirectToRoute('departments');
            }
        }
    
        return $this->render('admin/department/department_edit.html.twig', [
            'department' => $department,
            'formDepartment' => $formDepartment->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }
    
}
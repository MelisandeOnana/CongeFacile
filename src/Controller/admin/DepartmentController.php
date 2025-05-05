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


class DepartmentController extends AbstractController
{
   
    #[Route('/departments', name: 'departments')]
    public function index(DepartmentRepository $departmentRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(DepartmentSearchType::class);
        $form->handleRequest($request);

        $search = $form->get('search')->getData() ?? '';

        // Récupération des départements avec ou sans recherche
        $query = $departmentRepository->findBySearch($search);

        // Pagination : 10 départements par page
        $departments = $paginator->paginate(
            $query, // Query ou tableau
            $request->query->getInt('page', 1), // Numéro de la page
            10 // Limite par page
        );

        return $this->render('admin/department/index.html.twig', [
            'departments' => $departments,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/departments/new', name: 'department_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur a le rôle de manager
        $department = new Department();
        // Crée un nouveau département
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie si un département avec le même nom existe déjà
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
        // Vérifie si l'utilisateur a le rôle de manager
        $department = $departmentRepository->find($id);
    
        // Vérifie si le département existe
        if (! $department) {
            throw $this->createNotFoundException('Le département n\'existe pas.');
        }

        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);
    
        // Vérifie si le formulaire de suppression a été soumis
        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            // Récupère les collaborateurs liés au département
            $collaborators = $department->getCollaborators();

            // Vérifie si des collaborateurs sont liés au département
            if (count($collaborators) > 0) {
                $this->addFlash('error', 'Impossible de supprimer ce département car il est associé à des collaborateurs.');
                return $this->redirectToRoute('department_edit', ['id' => $id]);
            } else {
                try {
                    // Supprime le département
                    $entityManager->remove($department);
                    $entityManager->flush();
                    $this->addFlash('success', 'Le département a été supprimé avec succès.');
                } catch (Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de la suppression du département.');
                }
                return $this->redirectToRoute('departments');
            }
        }
        
        $formDepartment = $this->createForm(DepartmentType::class, $department);
        $formDepartment->handleRequest($request);

        // Vérifie si le formulaire d'édition a été soumis
        if ($formDepartment->isSubmitted() && $formDepartment->isValid()) {
            // Vérifie si un département avec le même nom existe déjà
            $existingDepartment = $departmentRepository->findOneBy(['name' => $department->getName()]);
            if ($existingDepartment && $existingDepartment->getId() !== $department->getId()) {
                $this->addFlash('error', 'Un département avec ce nom existe déjà.');

                return $this->redirectToRoute('department_edit', ['id' => $id]);
            } else {
                // Met à jour le département
                $entityManager->persist($department);
                $entityManager->flush();
                $this->addFlash('success', 'Le département a été mis à jour avec succès.');

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
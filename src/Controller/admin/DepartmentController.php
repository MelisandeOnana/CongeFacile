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

        // A DEPLACER OU UTILISER CRITERIA
        // Récupérer les départements avec ou sans recherche
        $query = $departmentRepository->createQueryBuilder('d')
            ->where('d.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery();

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
            // Vérifiez si des collaborateurs ou managers sont liés au département
            if ($department->getCollaborators()->count() > 0 || $department->getManagers()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer ce département car des collaborateurs ou managers y sont liés.');
            } else {
                try {
                    $entityManager->remove($department);
                    $entityManager->flush();
                    $this->addFlash('success', 'Le département a été supprimé avec succès.');
                } catch (Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de la suppression du département.');
                }
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
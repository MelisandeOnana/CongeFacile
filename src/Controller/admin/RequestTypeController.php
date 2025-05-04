<?php

namespace App\Controller\admin;

use App\Repository\RequestRepository;
use App\Repository\RequestTypeRepository;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RequestType;
use App\Form\RequestTypeFormType;
use App\Form\RequestTypeSearchType;

use App\Form\DeleteType;

#[IsGranted('ROLE_MANAGER')]
class RequestTypeController extends AbstractController
{
    #[Route('/request-type', name: 'request_types')]
    public function index(RequestTypeRepository $requestTypeRepository, RequestRepository $requestRepository, PaginatorInterface $paginator, HttpRequest $request): Response
    {
        $typesCounts = [];

        $form = $this->createForm(RequestTypeSearchType::class);
        $form->handleRequest($request);

        // Récupérer les valeurs des filtres depuis la requête
        $filterName = $request->query->get('name');
        $filterNumber = $request->query->get('number');

        $criteria = Criteria::create();

        if ($filterName) {
            $criteria->andWhere(Criteria::expr()->contains('name', $filterName));
        }

        $criteria->orderBy(['id' => 'DESC']);
        $filteredTypes = $requestTypeRepository->matching($criteria);

        foreach ($filteredTypes as $type) {
            $typesCounts[$type->getId()] = $requestRepository->countRequestsByRequestType($type);
        }

        if (null != $filterNumber) {
            $filteredTypesByNumber = [];
            foreach ($filteredTypes as $type) {
                if ($typesCounts[$type->getId()] == $filterNumber) {
                    $filteredTypesByNumber[] = $type;
                }
            }
            $filteredTypes = $filteredTypesByNumber;
        }

        $TypesPagination = $paginator->paginate(
            $filteredTypes, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit par page */
        );

        return $this->render('admin/request_type/index.html.twig', [
            'requestTypes' => $TypesPagination,
            'typesCounts' => $typesCounts,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/request-type/new', name: 'request_type_new')]
    public function new(HttpRequest $request, EntityManagerInterface $entityManager): Response
    {
        $requestType = new RequestType();
        $form = $this->createForm(RequestTypeFormType::class, $requestType);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingRequestType = $entityManager->getRepository(RequestType::class)->findOneBy(['name' => $requestType->getName()]);
            if ($existingRequestType) {
                $this->addFlash('error', 'Un type de demande avec ce nom existe déjà.');
                return $this->redirectToRoute('request_type_new');
            }

            $entityManager->persist($requestType);
            $entityManager->flush();

            $this->addFlash('success', 'Le type de demande a été ajouté avec succès.');

            return $this->redirectToRoute('request_types');
        }

        return $this->render('admin/request_type/request_type_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/request-type/edit/{id}', name: 'request_type_edit')]
    public function edit(RequestType $requestType, HttpRequest $request, EntityManagerInterface $entityManager, RequestRepository $requestRepository, $id): Response
    {
        $form = $this->createForm(RequestTypeFormType::class, $requestType);

        // Créer un formulaire de suppression
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        $requestTypeCount = $requestRepository->countRequestsByRequestType($requestType);

        // Gestion de la suppression
        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            if ($requestTypeCount > 0) {
                $this->addFlash('error', 'Impossible de supprimer ce type de demande car il est associé à des demandes.');
                return $this->redirectToRoute('request_type_edit', ['id' => $id]);
            } else {
                $entityManager->remove($requestType);
                $entityManager->flush();

                $this->addFlash('success', 'Le type de demande a été supprimé avec succès.');

                return $this->redirectToRoute('request_types');
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingRequestType = $entityManager->getRepository(RequestType::class)->findOneBy(['name' => $requestType->getName()]);
            if ($existingRequestType && $existingRequestType->getId() !== $requestType->getId()) {
                $this->addFlash('error', 'Un type de demande avec ce nom existe déjà.');
                return $this->redirectToRoute('request_type_edit', ['id' => $id]);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Le type de demande a été modifié avec succès.');

            return $this->redirectToRoute('request_types');
        }

        return $this->render('admin/request_type/request_type_edit.html.twig', [
            'form' => $form->createView(),
            'requestType' => $requestType,
            'formDelete' => $formDelete->createView(),
        ]);
    }
}

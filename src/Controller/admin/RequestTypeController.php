<?php

namespace App\Controller\admin;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\RequestType;
use App\Form\RequestTypeFormType;
use App\Form\RequestTypeSearchType;

use App\Form\DeleteType;
use App\Service\RequestTypeService;

#[IsGranted('ROLE_MANAGER')]
class RequestTypeController extends AbstractController
{
    #[Route('/request-type', name: 'request_types')]
    public function index(RequestTypeService $requestTypeService, PaginatorInterface $paginator, HttpRequest $request): Response
    {
        $form = $this->createForm(RequestTypeSearchType::class);
        $form->handleRequest($request);

        $filterName = $request->query->get('name');
        $filterNumber = $request->query->get('number');

        $filteredTypes = $requestTypeService->getFilteredTypes($filterName);
        $typesCounts = $requestTypeService->getTypesCounts($filteredTypes);

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
            $filteredTypes,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/request_type/index.html.twig', [
            'requestTypes' => $TypesPagination,
            'typesCounts' => $typesCounts,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/request-type/new', name: 'request_type_new')]
    public function new(HttpRequest $request, RequestTypeService $requestTypeService): Response
    {
        $requestType = new RequestType();
        $form = $this->createForm(RequestTypeFormType::class, $requestType);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$requestTypeService->isNameUnique($requestType)) {
                $this->addFlash('error', 'Un type de demande avec ce nom existe déjà.');
                return $this->redirectToRoute('request_type_new');
            }

            $requestTypeService->save($requestType);

            $this->addFlash('success', 'Le type de demande a été ajouté avec succès.');

            return $this->redirectToRoute('request_types');
        }

        return $this->render('admin/request_type/request_type_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/request-type/edit/{id}', name: 'request_type_edit')]
    public function edit(RequestType $requestType, HttpRequest $request, RequestTypeService $requestTypeService, $id): Response
    {
        $form = $this->createForm(RequestTypeFormType::class, $requestType);
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        $requestTypeCount = $requestTypeService->getTypesCounts([$requestType])[$requestType->getId()] ?? 0;

        // Gestion de la suppression
        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            if (!$requestTypeService->canDelete($requestType)) {
                $this->addFlash('error', 'Impossible de supprimer ce type de demande car il est associé à des demandes.');
                return $this->redirectToRoute('request_type_edit', ['id' => $id]);
            } else {
                $requestTypeService->delete($requestType);

                $this->addFlash('success', 'Le type de demande a été supprimé avec succès.');

                return $this->redirectToRoute('request_types');
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$requestTypeService->isNameUnique($requestType)) {
                $this->addFlash('error', 'Un type de demande avec ce nom existe déjà.');
                return $this->redirectToRoute('request_type_edit', ['id' => $id]);
            }
            $requestTypeService->save($requestType);

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

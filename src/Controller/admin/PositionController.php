<?php

namespace App\Controller\admin;

use App\Entity\Position;
use App\Form\DeleteType;
use App\Form\PositionType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\PositionSearchType;
use App\Service\PositionService;

#[IsGranted('ROLE_MANAGER')]
class PositionController extends AbstractController
{
    #[Route('/position', name: 'positions')]
    public function index(PositionService $positionService, PaginatorInterface $paginator, HttpRequest $request): Response
    {
        $form = $this->createForm(PositionSearchType::class);
        $form->handleRequest($request);

        $filterName = $request->query->get('name');
        $filterNumber = $request->query->get('number');

        $filteredPositions = $positionService->getFilteredPositions($filterName);
        $positionCounts = $positionService->getPositionCounts($filteredPositions);

        if (null !== $filterNumber) {
            $filteredPositionsByNumber = [];
            foreach ($filteredPositions as $position) {
                if ($positionCounts[$position->getId()] == $filterNumber) {
                    $filteredPositionsByNumber[] = $position;
                }
            }
            $filteredPositions = $filteredPositionsByNumber;
        }

        $PostionsPagination = $paginator->paginate(
            $filteredPositions,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/position/index.html.twig', [
            'positions' => $PostionsPagination,
            'positionCounts' => $positionCounts,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/position/edit/{id}', name: 'position_edit')]
    public function edit(Position $position, HttpRequest $request, PositionService $positionService): Response
    {
        $positionCount = $positionService->getPositionCounts([$position])[$position->getId()] ?? 0;

        $formPosition = $this->createForm(PositionType::class, $position);
        $formPosition->handleRequest($request);
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formPosition->isSubmitted() && $formPosition->isValid()) {
            if (!$positionService->isNameUnique($position)) {
                $this->addFlash('error', 'Un poste avec ce nom existe déjà.');
                return $this->redirectToRoute('position_edit', ['id' => $position->getId()]);
            }
            if ($positionService->save($position)) {
                $this->addFlash('success', 'Le poste a été modifié avec succès.');
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification du poste.');
            }
            return $this->redirectToRoute('positions');
        }

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            if (!$positionService->canDelete($position)) {
                $this->addFlash('error', 'Impossible de supprimer ce poste car il est associé à des collaborateurs.');
                return $this->redirectToRoute('position_edit', ['id' => $position->getId()]);
            }
            if ($positionService->delete($position)) {
                $this->addFlash('success', 'Le poste a été supprimé avec succès.');
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression du poste.');
            }
            return $this->redirectToRoute('positions');
        }

        return $this->render('admin/position/position_edit.html.twig', [
            'position' => $position,
            'formPosition' => $formPosition->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/position/new', name: 'position_new')]
    public function new(HttpRequest $request, PositionService $positionService): Response
    {
        $position = new Position();
        $formPosition = $this->createForm(PositionType::class, $position);
        $formPosition->handleRequest($request);

        if ($formPosition->isSubmitted() && $formPosition->isValid()) {
            if (!$positionService->isNameUnique($position)) {
                $this->addFlash('error', 'Un poste avec ce nom existe déjà.');
            } else {
                if ($positionService->save($position)) {
                    $this->addFlash('success', 'Le poste a été créé avec succès.');
                } else {
                    $this->addFlash('error', 'Une erreur est survenue lors de la création du poste.');
                }
                return $this->redirectToRoute('positions');
            }
        }

        return $this->render('admin/position/position_new.html.twig', [
            'formPosition' => $formPosition->createView(),
        ]);
    }

    #[Route('/position/delete/{id}', name: 'position_delete', methods: ['POST'])]
    public function delete(Position $position, PositionService $positionService): Response
    {
        if (!$positionService->canDelete($position)) {
            $this->addFlash('error', 'Impossible de supprimer ce poste car il est associé à des collaborateurs.');
        } else {
            if ($positionService->delete($position)) {
                $this->addFlash('success', 'Le poste a été supprimé avec succès.');
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression du poste.');
            }
        }
        return $this->redirectToRoute('positions');
    }
}

<?php

namespace App\Controller\admin;

use App\Entity\Position;
use App\Form\DeleteType;
use App\Form\PositionType;
use App\Repository\PersonRepository;
use App\Repository\PositionRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Exception;

#[IsGranted('ROLE_MANAGER')]
class PositionController extends AbstractController
{
    #[Route('/position', name: 'positions')]
    public function index(PositionRepository $positionRepository, PersonRepository $personRepository, PaginatorInterface $paginator, HttpRequest $request): Response
    {
        $positionCounts = [];

        // Récupérer les valeurs des filtres depuis la requête
        $filterName = $request->query->get('name');
        $filterNumber = $request->query->get('number');

        $criteria = Criteria::create();

        if ($filterName) {
            $criteria->andWhere(Criteria::expr()->contains('name', $filterName));
        }

        $criteria->orderBy(['id' => 'DESC']);
        $filteredPositions = $positionRepository->matching($criteria);

        foreach ($filteredPositions as $position) {
            $positionCounts[$position->getId()] = $personRepository->countByPosition($position);
        }

        if (null != $filterNumber) {
            $filteredPositionsByNumber = [];
            foreach ($filteredPositions as $position) {
                if ($positionCounts[$position->getId()] == $filterNumber) {
                    $filteredPositionsByNumber[] = $position;
                }
            }
            $filteredPositions = $filteredPositionsByNumber;
        }

        $PostionsPagination = $paginator->paginate(
            $filteredPositions, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            6 /* limit par page */
        );

        return $this->render('admin/position/index.html.twig', [
            'positions' => $PostionsPagination,
            'positionCounts' => $positionCounts,
        ]);
    }

    #[Route('/position/edit/{id}', name: 'position_edit')]
    public function edit(PositionRepository $positionRepository, $id, HttpRequest $request, EntityManagerInterface $entityManager, PersonRepository $personRepository): Response
    {
        $position = $positionRepository->find($id);

        if (! $position) {
            throw $this->createNotFoundException('Le poste n\'existe pas.');
        }

        $positionCount = $personRepository->countByPosition($position);

        $formPosition = $this->createForm(PositionType::class, $position);
        $formPosition->handleRequest($request);
        $formDelete = $this->createForm(DeleteType::class);

        if ($formPosition->isSubmitted() && $formPosition->isValid()) {
            $existingPosition = $positionRepository->findOneBy(['name' => $position->getName()]);
            if ($existingPosition && $existingPosition->getId() !== $position->getId()) {
                $this->addFlash('error', 'Un poste avec ce nom existe déjà.');

                return $this->redirectToRoute('position_edit', ['id' => $id]);
            } else {
                $entityManager->persist($position);
                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'Le poste a été créé avec succès.');
                } catch (Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de la création du poste.');
                }

                return $this->redirectToRoute('positions');
            }
        }

        return $this->render('admin/position/position_edit.html.twig', [
            'position' => $position,
            'formPosition' => $formPosition->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/position/new', name: 'position_new')]
    public function new(PositionRepository $positionRepository, HttpRequest $request, EntityManagerInterface $entityManager): Response
    {
        $position = new Position();
        $formPosition = $this->createForm(PositionType::class, $position);
        $formPosition->handleRequest($request);

        if ($formPosition->isSubmitted() && $formPosition->isValid()) {
            if ($positionRepository->findOneBy(['name' => $position->getName()])) {
                $this->addFlash('error', 'Un poste avec ce nom existe déjà.');
            } else {
                $entityManager->persist($position);
                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'Le poste a été créé avec succès.');
                } catch (Exception $e) {
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
    public function delete(Position $position, EntityManagerInterface $entityManager, PersonRepository $personRepository, HttpRequest $request): Response
    {
        $positionCount = $personRepository->countByPosition($position);

        if ($positionCount > 0) {
            $this->addFlash('error', 'Impossible de supprimer ce poste car il est associé à des collaborateurs.');
        } else {
            $entityManager->remove($position);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Le poste a été supprimé avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression du poste.');
            }
        }

        return $this->redirectToRoute('positions');
    }
}

<?php

namespace App\Controller;

use App\Entity\Position;
use App\Repository\PersonRepository;
use App\Repository\PositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PositionType;
use App\Form\DeleteType;

class PositionController extends AbstractController
{
    #[Route('/admin/position', name: 'positions')]
    public function index(PositionRepository $positionRepository, PersonRepository $personRepository,  PaginatorInterface $paginator, HttpRequest $request): Response
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

        if ($filterNumber != null) {
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
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit par page*/
        );

        return $this->render('default/administration/position/position.html.twig', [
            'positions' => $PostionsPagination,
            'positionCounts' => $positionCounts,
        ]);
    }

    #[Route('/admin/position/show/{id}', name: 'position_show')]
    public function show(PositionRepository $positionRepository, $id, HttpRequest $request, EntityManagerInterface $entityManager, PersonRepository $personRepository): Response
    {
        $position = $positionRepository->find($id);

        if (!$position) {
            throw $this->createNotFoundException('Le poste n\'existe pas.');
        }

        $positionCount = $personRepository->countByPosition($position);

        $delete = $request->query->get('delete');
        $formPosition = $this->createForm(PositionType::class, $position);
        $formPosition->handleRequest($request);
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($delete == 'true') {
            if ($formDelete->isSubmitted() && $formDelete->isValid()) {
                if ($positionCount > 0) {
                    $this->addFlash('error', 'Impossible de supprimer ce poste car il est associé à des collaborateurs.');
                    return $this->redirectToRoute('position_show', ['id' => $id]);
                } else {
                    $entityManager->remove($position);
                    $entityManager->flush();
                    return $this->redirectToRoute('positions');
                }
            }
        }

        if ($formPosition->isSubmitted() && $formPosition->isValid()) {
            $existingPosition = $positionRepository->findOneBy(['name' => $position->getName()]);
            if ($existingPosition && $existingPosition->getId() !== $position->getId()) {
                $this->addFlash('error', 'Un poste avec ce nom existe déjà.');
                return $this->redirectToRoute('position_show', ['id' => $id]);
            } else {

                $entityManager->persist($position);
                $entityManager->flush();
                return $this->redirectToRoute('positions');
            }
        }

        return $this->render('default/administration/position/position_show.html.twig', [
            'position' => $position,
            'formPosition' => $formPosition->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/admin/position/new', name: 'position_new')]
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
                $entityManager->flush();
                return $this->redirectToRoute('positions');
            }
        }

        return $this->render('default/administration/position/position_new.html.twig', [
            'formPosition' => $formPosition->createView(),
        ]);
    }
}
<?php

namespace App\Controller;

use App\Repository\PersonRepository;
use App\Repository\PositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Doctrine\Common\Collections\Criteria;

class PositionController extends AbstractController
{
    #[Route('/admin/positions', name: 'positions')]
    public function index(PositionRepository $positionRepository, PersonRepository $personRepository,  PaginatorInterface $paginator, HttpRequest $request): Response
    {
        $positions  = $positionRepository->findAll();
        $positionCounts = [];

        // Récupérer les valeurs des filtres depuis la requête
        $filterName = $request->query->get('name');
        $filterNumber = $request->query->get('number');

        foreach ($positions as $position) {
            $positionCounts[$position->getId()] = $personRepository->countByPosition($position);
        }

        $criteria = Criteria::create();

        if ($filterName) {
            $criteria->andWhere(Criteria::expr()->contains('name', $filterName));
        }

        $filteredPositions = $positionRepository->matching($criteria);

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

        return $this->render('default/administration/position/positions.html.twig', [
            'positions' => $PostionsPagination,
            'positionCounts' => $positionCounts,
        ]);
    }
}
<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Doctrine\Common\Collections\Criteria;
use App\Repository\RequestTypeRepository;
use App\Repository\RequestRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MANAGER')]
class RequestTypeController extends AbstractController
{
    #[Route('/request-type', name: 'request_types')]
    public function index(RequestTypeRepository $requestTypeRepository, RequestRepository $requestRepository,  PaginatorInterface $paginator, HttpRequest $request): Response
    {
        $typesCounts = [];

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

        if ($filterNumber != null) {
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
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit par page*/
        );

        return $this->render('default/admin/request_type/index.html.twig', [
            'requestTypes' => $TypesPagination,
            'typesCounts' => $typesCounts,
        ]);
    }
}
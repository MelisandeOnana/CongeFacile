<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class TeamController extends AbstractController
{
    #[Route('/team', name: 'team_index')]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $manager = $this->getUser()->getPerson();
        $queryBuilder = $userRepository->findByManager($manager);

        // Ajout des filtres
        $filters = [
            'lastName' => $request->query->get('filter_lastName'),
            'firstName' => $request->query->get('filter_firstName'),
            'email' => $request->query->get('filter_email'),
            'position' => $request->query->get('filter_position'),
            'vacationDays' => $request->query->get('filter_vacationDays'),
        ];

        foreach ($filters as $key => $value) {
            if ($value) {
                $queryBuilder->andWhere("user.person.$key LIKE :$key")
                             ->setParameter($key, '%' . $value . '%');
            }
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        $currentYear = (int) date('Y');
        $vacationDays = [];
        foreach ($pagination as $user) {
            $vacationDays[$user->getId()] = $userRepository->getVacationDaysForYear($user, $currentYear);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('team/_table.html.twig', [
                'pagination' => $pagination,
                'vacationDays' => $vacationDays,
            ]);
        }

        return $this->render('team/index.html.twig', [
            'pagination' => $pagination,
            'vacationDays' => $vacationDays,
        ]);
    }
}
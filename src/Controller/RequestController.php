<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Repository\RequestRepository;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use Exception;
class RequestController extends AbstractController
{
    #[Route('/historic', name: 'historic', methods: ['GET'])]
    public function historic(Request $request, RequestRepository $requestRepository): Response
    {
        $user = $this->getUser();
        $person = $user->getPerson();
        $requests = $requestRepository->findBy(['collaborator' => $person]);
        $requestType = $request->query->get('type');
        $requestDate = $request->query->get('date');
        $requestStart = $request->query->get('start');
        $requestEnd = $request->query->get('end');
        $requestNumber = $request->query->get('number');
        $requestAnswer = $request->query->get('answer');

        return $this->render('default/historic.html.twig', [
            'requests' => $requests,
            'type' => $requestType,
            'date' => $requestDate,
            'start' => $requestStart,
            'end' => $requestEnd,
            'number' => $requestNumber,
            'answer' => $requestAnswer,
        ]);
    }
}
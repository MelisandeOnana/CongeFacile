<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
class RequestController extends AbstractController
{
    #[Route('/historic', name: 'historic', methods: ['GET'])]
    public function historic(Request $request): Response
    {
        $requestType = $request->query->get('type');
        $requestDate = $request->query->get('date');
        $requestStart = $request->query->get('start');
        $requestEnd = $request->query->get('end');
        $requestNumber = $request->query->get('number');

        return $this->render('default/historic.html.twig', [
            'type' => $requestType,
            'date' => $requestDate,
            'start' => $requestStart,
            'end' => $requestEnd,
            'number' => $requestNumber,
        ]);
    }
}
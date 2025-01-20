<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Repository\RequestRepository;
use App\Repository\RequestTypeRepository;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Exception;

class RequestController extends AbstractController
{
    #[Route('/historic', name: 'historic', methods: ['GET'])]
    public function historic(Request $request, RequestRepository $requestRepository, RequestTypeRepository $requestTypeRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new Exception('L\'utilisateur n\'est pas de type User.');
        }
        $person = $user->getPerson();
        $requests = $requestRepository->findBy(['collaborator' => $person]);

        //Filtres
        $requestType = $requestTypeRepository->findAll();


        return $this->render('default/historic.html.twig', [
            'requests' => $requests,
            'requestTypes' => $requestType,
        ]);
    }
}
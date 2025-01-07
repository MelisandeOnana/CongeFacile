<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
class ConnectionController extends AbstractController
{
    #[Route('/', name: 'home_connection', methods: ['GET', 'POST'])]
    public function connection(AuthenticationUtils $authenticationUtils): Response
    {

        return $this->render('default/index.html.twig');
    }

    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
     
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/connexion.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/MotDePasseOublie', name: 'motdepasseoublie', methods: ['GET'])]
    public function motdepasseoublie(): Response
    {
        return $this->render('default/motdepasseoublie.html.twig');
    }

}

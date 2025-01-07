<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConnectionController extends AbstractController
{
    #[Route('/', name: 'home_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('default/connexion.html.twig');
    }

    #[Route('/MotDePasseOublie', name: 'motdepasseoublie', methods: ['GET'])]
    public function motdepasseoublie(): Response
    {
        return $this->render('default/motdepasseoublie.html.twig');
    }
}

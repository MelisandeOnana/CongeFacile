<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ErrorController extends AbstractController
{
    #[Route('/page-not-found', name: 'app_not_found')]
    public function show404(): Response
    {
        return $this->render('exception/404.html.twig', [], new Response('', Response::HTTP_NOT_FOUND));
    }

    #[Route('/access-denied', name: 'app_access_denied')]
    public function show403(): Response
    {
        return $this->render('exception/403.html.twig', [], new Response('', Response::HTTP_FORBIDDEN));
    }
}

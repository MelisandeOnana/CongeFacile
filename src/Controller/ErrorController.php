<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


class ErrorController extends AbstractController
{
    public function show(Request $request, Throwable $exception): Response
    {
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($statusCode == Response::HTTP_NOT_FOUND) {
            return $this->render('exception/404.html.twig', [], new Response('', Response::HTTP_NOT_FOUND));
        }

        if ($statusCode == Response::HTTP_FORBIDDEN) {
            return $this->render('exception/403.html.twig', [], new Response('', Response::HTTP_FORBIDDEN));
        }

        return $this->render('exception/error.html.twig', ['status_code' => $statusCode]);
    }
}
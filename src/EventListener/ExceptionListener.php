<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class ExceptionListener
{
    private RouterInterface $router;
    private Environment $twig;

    public function __construct(RouterInterface $router, Environment $twig)
    {
        $this->router = $router;
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            // Redirige vers la page 404 personnalisée
            $response = new Response(
                $this->twig->render('/exception/404.html.twig'),
                Response::HTTP_NOT_FOUND
            );
            $event->setResponse($response);
        }
        if ($exception instanceof AccessDeniedHttpException) {
            // Rediriger vers la page 403 personnalisée
            $response = new Response(
                $this->twig->render('/exception/403.html.twig'),
                Response::HTTP_NOT_FOUND
            );
            $event->setResponse($response);
        }
    }
}

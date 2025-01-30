<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExceptionListener
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            // Redirige vers la page 404 personnalisée
            $response = new RedirectResponse($this->router->generate('app_not_found'));
            $event->setResponse($response);
        }
        if ($exception instanceof AccessDeniedHttpException) {
            // Rediriger vers la page 403 personnalisée
            $response = new RedirectResponse($this->router->generate('app_access_denied'));
            $event->setResponse($response);
        }
    }

}

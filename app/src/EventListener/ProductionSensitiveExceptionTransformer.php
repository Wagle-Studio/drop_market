<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductionSensitiveExceptionTransformer
{
    /**
     * Transforms sensitive exceptions in the production environment to hide as much information as possible.
     *
     * This event listener is configured in `services_prod.yaml` and is only used in the production environment.
     *
     * AccessDeniedHttpException is used by voters to protect access to content.
     * It is not directly related to the authentication system.
     * Authentication behavior is defined in `security.yaml` to restrict access to unauthenticated users.
     *
     * Original error logs are preserved, as this transformation happens after logging.
     */
    public function __invoke(ExceptionEvent $event): ExceptionEvent
    {
        $exception = $event->getThrowable();

        if ($exception instanceof MethodNotAllowedHttpException || $exception instanceof AccessDeniedHttpException) {
            $event->setThrowable(new NotFoundHttpException("The requested page does not exist.", $exception));
        }

        return $event;
    }
}

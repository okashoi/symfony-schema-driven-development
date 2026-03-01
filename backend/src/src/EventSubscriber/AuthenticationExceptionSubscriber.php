<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Controller\Error\Error;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $isAuthError = $exception instanceof AuthenticationException
            || $exception instanceof AccessDeniedException
            || ($exception instanceof HttpException && $exception->getStatusCode() === Response::HTTP_UNAUTHORIZED);

        if ($isAuthError) {
            $event->setResponse(new JsonResponse(
                new Error(error: 'Full authentication is required to access this resource.'),
                Response::HTTP_UNAUTHORIZED,
            ));
        }
    }
}

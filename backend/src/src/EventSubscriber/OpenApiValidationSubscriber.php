<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Controller\Error\ValidationError;
use League\OpenAPIValidation\PSR7\Exception\NoOperation;
use League\OpenAPIValidation\PSR7\Exception\NoPath;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\ResponseValidator;
use League\OpenAPIValidation\PSR7\ServerRequestValidator;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OpenApiValidationSubscriber implements EventSubscriberInterface
{
    private const string OPERATION_ADDRESS_ATTRIBUTE = '_openapi_operation_address';

    private ServerRequestValidator $requestValidator;
    private ResponseValidator $responseValidator;
    private PsrHttpFactory $psrHttpFactory;

    public function __construct(string $schemaPath)
    {
        $builder = new ValidatorBuilder()->fromYamlFile($schemaPath);
        $this->requestValidator = $builder->getServerRequestValidator();
        $this->responseValidator = $builder->getResponseValidator();
        $this->psrHttpFactory = new PsrHttpFactory();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -128],
            KernelEvents::RESPONSE => ['onKernelResponse', -128],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $psrRequest = $this->psrHttpFactory->createRequest($event->getRequest());

        try {
            $operationAddress = $this->requestValidator->validate($psrRequest);
        } catch (NoPath|NoOperation) {
            return;
        } catch (ValidationFailed $e) {
            $event->setResponse(new JsonResponse(
                ValidationError::fromValidationFailed($e),
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ));

            return;
        }

        $event->getRequest()->attributes->set(self::OPERATION_ADDRESS_ATTRIBUTE, $operationAddress);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $operationAddress = $event->getRequest()->attributes->get(self::OPERATION_ADDRESS_ATTRIBUTE);
        if (!$operationAddress instanceof OperationAddress) {
            return;
        }

        $psrResponse = $this->psrHttpFactory->createResponse($event->getResponse());
        $this->responseValidator->validate($operationAddress, $psrResponse);
    }
}

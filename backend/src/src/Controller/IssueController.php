<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Error\Error;
use App\Controller\Error\ValidationError;
use App\Security\SecurityUser;
use App\UseCase\Issue\CloseIssue\CloseIssueUseCase;
use App\UseCase\Issue\CloseIssue\IssueAlreadyClosedException;
use App\UseCase\Issue\CreateIssue\CreateIssueInput;
use App\UseCase\Issue\CreateIssue\CreateIssueOutput;
use App\UseCase\Issue\CreateIssue\CreateIssueUseCase;
use App\UseCase\Issue\GetIssueDetail\GetIssueDetailOutput;
use App\UseCase\Issue\GetIssueDetail\GetIssueDetailUseCase;
use App\UseCase\Issue\IssueNotFoundException;
use App\UseCase\Issue\ListIssues\ListIssuesOutput;
use App\UseCase\Issue\ListIssues\ListIssuesUseCase;
use App\UseCase\Issue\UpdateIssue\UpdateIssueInput;
use App\UseCase\Issue\UpdateIssue\UpdateIssueUseCase;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('api/issue')]
class IssueController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'List of issues',
        content: new OA\JsonContent(ref: new Model(type: ListIssuesOutput::class)),
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    public function list(
        #[CurrentUser] SecurityUser $user,
        ListIssuesUseCase $useCase,
    ): Response {
        $output = $useCase->execute();

        return JsonResponse::fromJsonString($this->serializer->serialize($output, 'json'), Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Issue detail',
        content: new OA\JsonContent(ref: new Model(type: GetIssueDetailOutput::class)),
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    #[OA\Response(
        response: 404,
        description: 'Issue not found',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    public function getDetail(
        #[CurrentUser] SecurityUser $user,
        Uuid $id,
        GetIssueDetailUseCase $useCase,
    ): Response {
        try {
            $output = $useCase->execute($id);
        } catch (IssueNotFoundException) {
            return new JsonResponse(new Error(error: 'Issue not found.'), Response::HTTP_NOT_FOUND);
        }

        return JsonResponse::fromJsonString($this->serializer->serialize($output, 'json'), Response::HTTP_OK);
    }

    #[Route('', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: CreateIssueInput::class)),
    )]
    #[OA\Response(
        response: 201,
        description: 'Issue created',
        content: new OA\JsonContent(ref: new Model(type: CreateIssueOutput::class)),
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation failed',
        content: new OA\JsonContent(ref: new Model(type: ValidationError::class)),
    )]
    public function create(
        #[CurrentUser] SecurityUser $user,
        #[MapRequestPayload] CreateIssueInput $input,
        CreateIssueUseCase $useCase,
    ): Response {
        $output = $useCase->execute($input);

        return JsonResponse::fromJsonString($this->serializer->serialize($output, 'json'), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: UpdateIssueInput::class)),
    )]
    #[OA\Response(
        response: 200,
        description: 'Issue updated',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    #[OA\Response(
        response: 404,
        description: 'Issue not found',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation failed',
        content: new OA\JsonContent(ref: new Model(type: ValidationError::class)),
    )]
    public function update(
        #[CurrentUser] SecurityUser $user,
        Uuid $id,
        #[MapRequestPayload] UpdateIssueInput $input,
        UpdateIssueUseCase $useCase,
    ): Response {
        try {
            $useCase->execute($id, $input);
        } catch (IssueNotFoundException) {
            return new JsonResponse(new Error(error: 'Issue not found.'), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }

    #[Route('/{id}/close', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Issue closed',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    #[OA\Response(
        response: 404,
        description: 'Issue not found',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    #[OA\Response(
        response: 409,
        description: 'Issue already closed',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    public function close(
        #[CurrentUser] SecurityUser $user,
        Uuid $id,
        CloseIssueUseCase $useCase,
    ): Response {
        try {
            $useCase->execute($id);
        } catch (IssueNotFoundException) {
            return new JsonResponse(new Error(error: 'Issue not found.'), Response::HTTP_NOT_FOUND);
        } catch (IssueAlreadyClosedException) {
            return new JsonResponse(new Error(error: 'Issue is already closed.'), Response::HTTP_CONFLICT);
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }
}

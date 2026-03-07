<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Error\Error;
use App\Controller\Error\ValidationError;
use App\Security\SecurityUser;
use App\UseCase\Assignment\AssignIssue\AssignIssueInput;
use App\UseCase\Assignment\AssignIssue\AssignIssueOutput;
use App\UseCase\Assignment\AssignIssue\AssignIssueUseCase;
use App\UseCase\Issue\IssueNotFoundException;
use App\UseCase\User\GetUserDetail\UserNotFoundException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/assignment')]
class AssignmentController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: AssignIssueInput::class)),
    )]
    #[OA\Response(
        response: 201,
        description: 'Assignment created',
        content: new OA\JsonContent(ref: new Model(type: AssignIssueOutput::class)),
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    #[OA\Response(
        response: 404,
        description: 'Issue or user not found',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation failed',
        content: new OA\JsonContent(ref: new Model(type: ValidationError::class)),
    )]
    public function assign(
        #[CurrentUser] SecurityUser $user,
        #[MapRequestPayload] AssignIssueInput $input,
        AssignIssueUseCase $useCase,
    ): Response {
        try {
            $output = $useCase->execute($input);
        } catch (IssueNotFoundException) {
            return new JsonResponse(new Error(error: 'Issue not found.'), Response::HTTP_NOT_FOUND);
        } catch (UserNotFoundException) {
            return new JsonResponse(new Error(error: 'User not found.'), Response::HTTP_NOT_FOUND);
        }

        return JsonResponse::fromJsonString($this->serializer->serialize($output, 'json'), Response::HTTP_CREATED);
    }
}

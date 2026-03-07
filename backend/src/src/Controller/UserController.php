<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Error\Error;
use App\Security\SecurityUser;
use App\UseCase\User\GetUserDetail\GetUserDetailOutput;
use App\UseCase\User\GetUserDetail\GetUserDetailUseCase;
use App\UseCase\User\GetUserDetail\UserNotFoundException;
use App\UseCase\User\ListUsers\ListUsersOutput;
use App\UseCase\User\ListUsers\ListUsersUseCase;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('api/user')]
class UserController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'List of users',
        content: new OA\JsonContent(ref: new Model(type: ListUsersOutput::class)),
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    public function list(
        #[CurrentUser] SecurityUser $user,
        ListUsersUseCase $useCase,
    ): Response {
        $output = $useCase->execute();

        return JsonResponse::fromJsonString($this->serializer->serialize($output, 'json'), Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'User detail',
        content: new OA\JsonContent(ref: new Model(type: GetUserDetailOutput::class)),
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    public function getDetail(
        #[CurrentUser] SecurityUser $user,
        Uuid $id,
        GetUserDetailUseCase $useCase,
    ): Response {
        try {
            $output = $useCase->execute($id);
        } catch (UserNotFoundException) {
            return new JsonResponse(new Error(error: 'User not found.'), Response::HTTP_NOT_FOUND);
        }

        return JsonResponse::fromJsonString($this->serializer->serialize($output, 'json'), Response::HTTP_OK);
    }
}

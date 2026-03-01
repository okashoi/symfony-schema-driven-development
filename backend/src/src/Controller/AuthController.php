<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Error\Error;
use App\Controller\Error\ValidationError;
use App\UseCase\Auth\Login\InvalidCredentialsException;
use App\UseCase\Auth\Login\LoginInput;
use App\UseCase\Auth\Login\LoginUseCase;
use App\UseCase\Auth\Signup\SignupInput;
use App\UseCase\Auth\Signup\SignupOutput;
use App\UseCase\Auth\Signup\SignupUseCase;
use App\UseCase\Auth\Signup\UserAlreadyExistsException;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/auth')]
class AuthController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('/signup', methods: ['POST'])]
    #[Security]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: SignupInput::class)),
    )]
    #[OA\Response(
        response: 201,
        description: 'User created',
        content: new OA\JsonContent(ref: new Model(type: SignupOutput::class)),
    )]
    #[OA\Response(
        response: 409,
        description: 'User already exists',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation failed',
        content: new OA\JsonContent(ref: new Model(type: ValidationError::class)),
    )]
    public function signup(
        #[MapRequestPayload] SignupInput $input,
        SignupUseCase $useCase,
    ): Response {
        try {
            $output = $useCase->execute($input);
        } catch (UserAlreadyExistsException) {
            return new JsonResponse(new Error(error: 'User already exists.'), Response::HTTP_CONFLICT);
        }

        return JsonResponse::fromJsonString($this->serializer->serialize($output, 'json'), Response::HTTP_CREATED);
    }

    #[Route('/login', methods: ['POST'])]
    #[Security]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: LoginInput::class)),
    )]
    #[OA\Response(
        response: 200,
        description: 'Login successful',
        headers: [new OA\Header(
            header: 'Set-Cookie',
            description: 'Session cookie',
            schema: new OA\Schema(type: 'string', example: 'PHPSESSID=abc123; Path=/; HttpOnly'),
        )],
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation failed',
        content: new OA\JsonContent(ref: new Model(type: ValidationError::class)),
    )]
    public function login(
        #[MapRequestPayload] LoginInput $input,
        LoginUseCase $useCase,
        Request $request,
        TokenStorageInterface $tokenStorage,
    ): Response {
        try {
            $output = $useCase->execute($input);
        } catch (InvalidCredentialsException) {
            return new JsonResponse(new Error(error: 'Invalid credentials.'), Response::HTTP_UNAUTHORIZED);
        }

        $user = $output->user;
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage->setToken($token);
        $request->getSession()->set('_security_main', serialize($token));

        return new JsonResponse(null, Response::HTTP_OK);
    }

    #[Route('/logout', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Logout successful',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(ref: new Model(type: Error::class)),
    )]
    public function logout(
        Request $request,
        TokenStorageInterface $tokenStorage,
    ): Response {
        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        return new JsonResponse(null, Response::HTTP_OK);
    }
}

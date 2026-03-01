<?php

declare(strict_types=1);

namespace App\UseCase\Auth\Login;

use App\Entity\UserDetail;
use App\Entity\UserPasswordAuth;
use App\Security\SecurityUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class LoginUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function execute(LoginInput $input): LoginOutput
    {
        $userDetail = $this->entityManager->getRepository(UserDetail::class)->findOneBy(['name' => $input->name]);
        if ($userDetail === null) {
            throw new InvalidCredentialsException();
        }

        $passwordAuth = $this->entityManager->find(UserPasswordAuth::class, $userDetail->userId);
        if ($passwordAuth === null) {
            throw new InvalidCredentialsException();
        }

        \assert($userDetail->name !== '');

        $user = new SecurityUser(
            $userDetail->userId,
            $userDetail->name,
            $passwordAuth->password,
        );

        if (!$this->passwordHasher->isPasswordValid($user, $input->password)) {
            throw new InvalidCredentialsException();
        }

        return new LoginOutput(user: $user);
    }
}

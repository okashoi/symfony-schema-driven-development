<?php

declare(strict_types=1);

namespace App\UseCase\Auth\Signup;

use App\Entity\User;
use App\Entity\UserDetail;
use App\Entity\UserPasswordAuth;
use App\Security\SecurityUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class SignupUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    /**
     * @throws UserAlreadyExistsException
     */
    public function execute(SignupInput $input): SignupOutput
    {
        $existingUser = $this->entityManager->getRepository(UserDetail::class)->findOneBy(['name' => $input->name]);
        if ($existingUser !== null) {
            throw new UserAlreadyExistsException();
        }

        \assert($input->name !== '');

        $hashedPassword = $this->passwordHasher->hashPassword(new SecurityUser(
            Uuid::v4(),
            $input->name,
            '',
        ), $input->password);

        $user = new User();
        $this->entityManager->persist($user);

        $userDetail = new UserDetail();
        $userDetail->userId = $user->id;
        $userDetail->name = $input->name;
        $this->entityManager->persist($userDetail);

        $userPasswordAuth = new UserPasswordAuth();
        $userPasswordAuth->userId = $user->id;
        $userPasswordAuth->password = $hashedPassword;
        $this->entityManager->persist($userPasswordAuth);

        $this->entityManager->flush();

        return new SignupOutput(id: $user->id);
    }
}

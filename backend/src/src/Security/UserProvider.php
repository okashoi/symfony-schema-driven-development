<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\UserDetail;
use App\Entity\UserPasswordAuth;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<SecurityUser>
 */
class UserProvider implements UserProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return SecurityUser::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $userDetail = $this->entityManager->getRepository(UserDetail::class)->findOneBy(['name' => $identifier]);
        if ($userDetail === null) {
            throw new UserNotFoundException(\sprintf('User "%s" not found.', $identifier));
        }

        $passwordAuth = $this->entityManager->find(UserPasswordAuth::class, $userDetail->userId);
        if ($passwordAuth === null) {
            throw new UserNotFoundException(\sprintf('User "%s" has no password authentication.', $identifier));
        }

        \assert($userDetail->name !== '');

        return new SecurityUser(
            $userDetail->userId,
            $userDetail->name,
            $passwordAuth->password,
        );
    }
}

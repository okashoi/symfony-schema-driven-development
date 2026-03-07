<?php

declare(strict_types=1);

namespace App\UseCase\User\ListUsers;

use App\Entity\UserDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class ListUsersUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(): ListUsersOutput
    {
        /** @var list<array{userId: Uuid, name: string}> $rows */
        $rows = $this->entityManager->createQueryBuilder()
            ->select('ud.userId', 'ud.name')
            ->from(UserDetail::class, 'ud')
            ->orderBy('ud.name', 'ASC')
            ->getQuery()
            ->getResult();

        $items = array_map(
            fn (array $row) => new UserListItem(
                id: $row['userId'],
                name: $row['name'],
            ),
            $rows,
        );

        return new ListUsersOutput(users: $items);
    }
}

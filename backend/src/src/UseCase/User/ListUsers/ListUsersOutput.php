<?php

declare(strict_types=1);

namespace App\UseCase\User\ListUsers;

readonly class ListUsersOutput
{
    /**
     * @param list<UserListItem> $users
     */
    public function __construct(
        public array $users,
    ) {
    }
}

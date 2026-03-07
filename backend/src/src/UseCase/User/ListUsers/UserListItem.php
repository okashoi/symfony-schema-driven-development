<?php

declare(strict_types=1);

namespace App\UseCase\User\ListUsers;

use Symfony\Component\Uid\Uuid;

readonly class UserListItem
{
    public function __construct(
        public Uuid $id,
        public string $name,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\UseCase\Auth\Login;

use App\Security\SecurityUser;

readonly class LoginOutput
{
    public function __construct(
        public SecurityUser $user,
    ) {
    }
}

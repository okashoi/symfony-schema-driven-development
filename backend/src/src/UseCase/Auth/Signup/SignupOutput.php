<?php

declare(strict_types=1);

namespace App\UseCase\Auth\Signup;

use Symfony\Component\Uid\Uuid;

readonly class SignupOutput
{
    public function __construct(
        public Uuid $id,
    ) {
    }
}

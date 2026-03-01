<?php

declare(strict_types=1);

namespace App\UseCase\Auth\Login;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class LoginInput
{
    public function __construct(
        #[OA\Property(minLength: 1)]
        #[Assert\NotBlank]
        public string $name,

        #[OA\Property(minLength: 1)]
        #[Assert\NotBlank]
        public string $password,
    ) {
    }
}

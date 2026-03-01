<?php

declare(strict_types=1);

namespace App\UseCase\Auth\Signup;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class SignupInput
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

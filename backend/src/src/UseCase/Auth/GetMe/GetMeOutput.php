<?php

declare(strict_types=1);

namespace App\UseCase\Auth\GetMe;

use Symfony\Component\Uid\Uuid;

readonly class GetMeOutput
{
    public function __construct(
        public Uuid $id,
        public string $name,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Error;

readonly class Error
{
    public function __construct(
        public string $error,
    ) {
    }
}

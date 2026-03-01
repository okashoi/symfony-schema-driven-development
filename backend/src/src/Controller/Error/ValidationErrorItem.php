<?php

declare(strict_types=1);

namespace App\Controller\Error;

readonly class ValidationErrorItem
{
    public function __construct(
        public ?string $field,
        public string $message,
    ) {
    }
}

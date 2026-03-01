<?php

declare(strict_types=1);

namespace App\Controller\Error;

use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\Schema\Exception\SchemaMismatch;

readonly class ValidationError
{
    /**
     * @param list<ValidationErrorItem> $errors
     */
    public function __construct(
        public array $errors,
    ) {
    }

    public static function fromValidationFailed(ValidationFailed $exception): self
    {
        $previous = $exception->getPrevious();

        if (!$previous instanceof SchemaMismatch) {
            return new self([new ValidationErrorItem(field: null, message: $exception->getMessage())]);
        }

        $field = null;
        $breadCrumb = $previous->dataBreadCrumb();
        if ($breadCrumb !== null) {
            /** @var list<string> $parts */
            $parts = $breadCrumb->buildChain();
            $field = implode('.', $parts) ?: null;
        }

        return new self([new ValidationErrorItem(field: $field, message: $previous->getMessage())]);
    }
}

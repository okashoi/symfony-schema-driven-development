<?php

declare(strict_types=1);

namespace App\UseCase\Issue\UpdateIssue;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateIssueInput
{
    public function __construct(
        #[OA\Property(minLength: 1)]
        #[Assert\NotBlank]
        public string $summary,
    ) {
    }
}

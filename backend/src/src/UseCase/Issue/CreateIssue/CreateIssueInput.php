<?php

declare(strict_types=1);

namespace App\UseCase\Issue\CreateIssue;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateIssueInput
{
    public function __construct(
        #[OA\Property(minLength: 1)]
        #[Assert\NotBlank]
        public string $summary,
    ) {
    }
}

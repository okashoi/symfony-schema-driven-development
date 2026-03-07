<?php

declare(strict_types=1);

namespace App\UseCase\Issue\GetIssueDetail;

use Symfony\Component\Uid\Uuid;

readonly class GetIssueDetailOutput
{
    public function __construct(
        public Uuid $id,
        public string $summary,
        public ?Assignee $assignee,
        public ?\DateTimeImmutable $closedAt,
        public \DateTimeImmutable $updatedAt,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}

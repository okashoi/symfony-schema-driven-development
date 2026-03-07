<?php

declare(strict_types=1);

namespace App\UseCase\Issue\ListIssues;

use Symfony\Component\Uid\Uuid;

readonly class IssueListItem
{
    public function __construct(
        public Uuid $id,
        public string $summary,
        public string $assignee,
        public bool $isClosed,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}

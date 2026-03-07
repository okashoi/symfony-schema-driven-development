<?php

declare(strict_types=1);

namespace App\UseCase\Issue\ListIssues;

readonly class ListIssuesOutput
{
    /**
     * @param list<IssueListItem> $issues
     */
    public function __construct(
        public array $issues,
    ) {
    }
}

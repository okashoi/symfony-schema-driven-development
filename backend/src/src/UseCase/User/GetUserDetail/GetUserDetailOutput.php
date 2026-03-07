<?php

declare(strict_types=1);

namespace App\UseCase\User\GetUserDetail;

use Symfony\Component\Uid\Uuid;

readonly class GetUserDetailOutput
{
    /**
     * @param list<AssignedIssue> $assignedIssues
     */
    public function __construct(
        public Uuid $id,
        public string $name,
        public array $assignedIssues,
    ) {
    }
}

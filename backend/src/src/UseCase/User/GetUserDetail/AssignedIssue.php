<?php

declare(strict_types=1);

namespace App\UseCase\User\GetUserDetail;

use Symfony\Component\Uid\Uuid;

readonly class AssignedIssue
{
    public function __construct(
        public Uuid $id,
        public string $summary,
        public bool $isClosed,
    ) {
    }
}

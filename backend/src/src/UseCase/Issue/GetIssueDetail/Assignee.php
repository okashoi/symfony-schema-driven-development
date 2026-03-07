<?php

declare(strict_types=1);

namespace App\UseCase\Issue\GetIssueDetail;

use Symfony\Component\Uid\Uuid;

readonly class Assignee
{
    public function __construct(
        public Uuid $id,
        public string $name,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\UseCase\Issue\CreateIssue;

use Symfony\Component\Uid\Uuid;

readonly class CreateIssueOutput
{
    public function __construct(
        public Uuid $id,
    ) {
    }
}

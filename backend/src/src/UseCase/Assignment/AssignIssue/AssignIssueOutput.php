<?php

declare(strict_types=1);

namespace App\UseCase\Assignment\AssignIssue;

use Symfony\Component\Uid\Uuid;

readonly class AssignIssueOutput
{
    public function __construct(
        public Uuid $id,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\UseCase\Assignment\AssignIssue;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class AssignIssueInput
{
    public function __construct(
        #[Assert\NotBlank]
        public Uuid $issueId,

        #[Assert\NotBlank]
        public Uuid $assigneeId,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\UseCase\Assignment\AssignIssue;

use App\Entity\Issue;
use App\Entity\IssueAssignment;
use App\Entity\UserDetail;
use App\UseCase\Issue\IssueNotFoundException;
use App\UseCase\User\GetUserDetail\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

readonly class AssignIssueUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws IssueNotFoundException
     * @throws UserNotFoundException
     */
    public function execute(AssignIssueInput $input): AssignIssueOutput
    {
        $issue = $this->entityManager->find(Issue::class, $input->issueId);
        if ($issue === null) {
            throw new IssueNotFoundException();
        }

        $user = $this->entityManager->find(UserDetail::class, $input->assigneeId);
        if ($user === null) {
            throw new UserNotFoundException();
        }

        $assignment = new IssueAssignment();
        $assignment->issueId = $input->issueId;
        $assignment->assigneeId = $input->assigneeId;
        $this->entityManager->persist($assignment);

        $this->entityManager->flush();

        return new AssignIssueOutput(id: $assignment->id);
    }
}

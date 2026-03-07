<?php

declare(strict_types=1);

namespace App\UseCase\Issue\CreateIssue;

use App\Entity\Issue;
use App\Entity\IssueDetailActive;
use App\Entity\IssueDetailValue;
use Doctrine\ORM\EntityManagerInterface;

readonly class CreateIssueUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(CreateIssueInput $input): CreateIssueOutput
    {
        $issue = new Issue();
        $this->entityManager->persist($issue);

        $detailValue = new IssueDetailValue();
        $detailValue->issueId = $issue->id;
        $detailValue->summary = $input->summary;
        $this->entityManager->persist($detailValue);

        $detailActive = new IssueDetailActive();
        $detailActive->issueId = $issue->id;
        $detailActive->detailValueId = $detailValue->id;
        $this->entityManager->persist($detailActive);

        $this->entityManager->flush();

        return new CreateIssueOutput(id: $issue->id);
    }
}

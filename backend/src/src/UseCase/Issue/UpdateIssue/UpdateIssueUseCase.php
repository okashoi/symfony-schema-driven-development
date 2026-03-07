<?php

declare(strict_types=1);

namespace App\UseCase\Issue\UpdateIssue;

use App\Entity\IssueDetailActive;
use App\Entity\IssueDetailValue;
use App\UseCase\Issue\IssueNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class UpdateIssueUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws IssueNotFoundException
     */
    public function execute(Uuid $id, UpdateIssueInput $input): void
    {
        $detailActive = $this->entityManager->find(IssueDetailActive::class, $id);
        if ($detailActive === null) {
            throw new IssueNotFoundException();
        }

        $detailValue = new IssueDetailValue();
        $detailValue->issueId = $id;
        $detailValue->summary = $input->summary;
        $this->entityManager->persist($detailValue);

        $detailActive->detailValueId = $detailValue->id;

        $this->entityManager->flush();
    }
}

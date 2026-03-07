<?php

declare(strict_types=1);

namespace App\UseCase\Issue\CloseIssue;

use App\Entity\Issue;
use App\Entity\IssueClose;
use App\UseCase\Issue\IssueNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class CloseIssueUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws IssueNotFoundException
     * @throws IssueAlreadyClosedException
     */
    public function execute(Uuid $id): void
    {
        $issue = $this->entityManager->find(Issue::class, $id);
        if ($issue === null) {
            throw new IssueNotFoundException();
        }

        $existingClose = $this->entityManager->find(IssueClose::class, $id);
        if ($existingClose !== null) {
            throw new IssueAlreadyClosedException();
        }

        $issueClose = new IssueClose();
        $issueClose->issueId = $id;
        $this->entityManager->persist($issueClose);

        $this->entityManager->flush();
    }
}

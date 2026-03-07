<?php

declare(strict_types=1);

namespace App\UseCase\Issue\GetIssueDetail;

use App\Entity\IssueAssignees;
use App\Entity\IssueDetail;
use App\Entity\UserDetail;
use App\UseCase\Issue\IssueNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class GetIssueDetailUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws IssueNotFoundException
     */
    public function execute(Uuid $id): GetIssueDetailOutput
    {
        $issue = $this->entityManager->find(IssueDetail::class, $id);
        if ($issue === null) {
            throw new IssueNotFoundException();
        }

        $assignee = $this->getAssignee($id);

        return new GetIssueDetailOutput(
            id: $issue->id,
            summary: $issue->summary,
            assignee: $assignee,
            closedAt: $issue->closedAt,
            updatedAt: $issue->updatedAt,
            createdAt: $issue->createdAt,
        );
    }

    private function getAssignee(Uuid $issueId): ?Assignee
    {
        /** @var array{assigneeId: Uuid, name: string}|null $row */
        $row = $this->entityManager->createQueryBuilder()
            ->select('ia.assigneeId', 'ud.name')
            ->from(IssueAssignees::class, 'ia')
            ->innerJoin(UserDetail::class, 'ud', 'WITH', 'ud.userId = ia.assigneeId')
            ->where('ia.issueId = :issueId')
            ->setParameter('issueId', $issueId)
            ->getQuery()
            ->getOneOrNullResult();

        if ($row === null) {
            return null;
        }

        return new Assignee(
            id: $row['assigneeId'],
            name: $row['name'],
        );
    }
}

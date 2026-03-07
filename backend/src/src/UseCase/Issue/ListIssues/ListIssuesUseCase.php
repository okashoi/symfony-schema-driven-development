<?php

declare(strict_types=1);

namespace App\UseCase\Issue\ListIssues;

use App\Entity\IssueAssignees;
use App\Entity\IssueDetail;
use App\Entity\UserDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class ListIssuesUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(): ListIssuesOutput
    {
        /** @var list<array{id: Uuid, summary: string, assignee: string, closedAt: ?\DateTimeImmutable, createdAt: \DateTimeImmutable}> $rows */
        $rows = $this->entityManager->createQueryBuilder()
            ->select('id.id', 'id.summary', "COALESCE(ud.name, '') AS assignee", 'id.closedAt', 'id.createdAt')
            ->from(IssueDetail::class, 'id')
            ->leftJoin(IssueAssignees::class, 'ia', 'WITH', 'ia.issueId = id.id')
            ->leftJoin(UserDetail::class, 'ud', 'WITH', 'ud.userId = ia.assigneeId')
            ->orderBy('id.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $items = array_map(
            fn (array $row) => new IssueListItem(
                id: $row['id'],
                summary: $row['summary'],
                assignee: $row['assignee'],
                isClosed: $row['closedAt'] !== null,
                createdAt: $row['createdAt'],
            ),
            $rows,
        );

        return new ListIssuesOutput(issues: $items);
    }
}

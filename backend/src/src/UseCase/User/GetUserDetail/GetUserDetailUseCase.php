<?php

declare(strict_types=1);

namespace App\UseCase\User\GetUserDetail;

use App\Entity\IssueAssignees;
use App\Entity\IssueDetail;
use App\Entity\UserDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class GetUserDetailUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws UserNotFoundException
     */
    public function execute(Uuid $id): GetUserDetailOutput
    {
        $userDetail = $this->entityManager->find(UserDetail::class, $id);

        if ($userDetail === null) {
            throw new UserNotFoundException();
        }

        /** @var list<array{id: Uuid, summary: string, closedAt: ?\DateTimeImmutable}> $rows */
        $rows = $this->entityManager->createQueryBuilder()
            ->select('id.id', 'id.summary', 'id.closedAt')
            ->from(IssueAssignees::class, 'ia')
            ->join(IssueDetail::class, 'id', 'WITH', 'id.id = ia.issueId')
            ->where('ia.assigneeId = :userId')
            ->setParameter('userId', $id)
            ->getQuery()
            ->getResult();

        $assignedIssues = array_map(
            fn (array $row) => new AssignedIssue(
                id: $row['id'],
                summary: $row['summary'],
                isClosed: $row['closedAt'] !== null,
            ),
            $rows,
        );

        return new GetUserDetailOutput(
            id: $userDetail->userId,
            name: $userDetail->name,
            assignedIssues: $assignedIssues,
        );
    }
}

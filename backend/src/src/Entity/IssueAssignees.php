<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(readOnly: true)]
class IssueAssignees
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    public Uuid $issueId;

    #[ORM\Column(type: UuidType::NAME)]
    public Uuid $assigneeId;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    public \DateTimeImmutable $assignedAt;
}

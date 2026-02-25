<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class IssueDetailActive
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    public Uuid $issueId;

    #[ORM\Column(type: UuidType::NAME)]
    public Uuid $detailValueId;
}

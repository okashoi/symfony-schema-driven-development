<?php

declare(strict_types=1);

namespace MyLib\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MyLib\Doctrine\DBAL\Types\DateTimeTzImmutableType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DateTimeTzImmutableTypeTest extends TestCase
{
    #[Test]
    public function convertToDatabaseValue(): void
    {
        $actual = new DateTimeTzImmutableType()
            ->convertToDatabaseValue(
                new \DateTimeImmutable('2026-01-01'),
                $this->createStub(AbstractPlatform::class),
            );

        $this->assertSame('2026-01-01 00:00:00 +09:00', $actual);
    }

    #[Test]
    public function convertToPHPValue(): void
    {
        $actual = new DateTimeTzImmutableType()
            ->convertToPHPValue(
                '2026-01-01 00:00:00 +09:00',
                $this->createStub(AbstractPlatform::class),
            );

        $this->assertInstanceOf(\DateTimeImmutable::class, $actual);
        $this->assertEquals(new \DateTimeImmutable('2026-01-01 00:00:00 +09:00'), $actual);
    }
}

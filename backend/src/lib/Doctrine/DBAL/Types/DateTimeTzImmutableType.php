<?php

declare(strict_types=1);

namespace MyLib\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeTzImmutableType as BaseType;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;

class DateTimeTzImmutableType extends BaseType
{
    private const string FORMAT = 'Y-m-d H:i:s p';

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value->format(self::FORMAT);
        }

        throw InvalidType::new($value, static::class, ['null', \DateTimeImmutable::class]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?\DateTimeImmutable
    {
        if ($value === null || $value instanceof \DateTimeImmutable) {
            return $value;
        }
        assert(is_string($value));

        $dateTime = \DateTimeImmutable::createFromFormat(self::FORMAT, $value);

        if ($dateTime !== false) {
            return $dateTime;
        }

        throw InvalidFormat::new($value, static::class, self::FORMAT);
    }
}

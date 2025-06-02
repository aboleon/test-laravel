<?php

namespace App\Doctrine\Type;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TimestampType extends Type
{
    const TIMESTAMP = 'timestamp'; // Custom type name

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'TIMESTAMP';
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DateTime
    {
        return $value !== null ? new DateTime($value) : null;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        return $value !== null ? $value->format($platform->getDateTimeFormatString()) : null;
    }

    public function getName(): string
    {
        return self::TIMESTAMP;
    }
}

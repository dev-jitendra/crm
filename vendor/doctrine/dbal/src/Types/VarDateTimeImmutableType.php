<?php

namespace Doctrine\DBAL\Types;

use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\Deprecations\Deprecation;

use function date_create_immutable;


class VarDateTimeImmutableType extends VarDateTimeType
{
    
    public function getName()
    {
        return Types::DATETIME_IMMUTABLE;
    }

    
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof DateTimeImmutable) {
            return $value->format($platform->getDateTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', DateTimeImmutable::class],
        );
    }

    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof DateTimeImmutable) {
            return $value;
        }

        $dateTime = date_create_immutable($value);

        if ($dateTime === false) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $dateTime;
    }

    
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            '%s is deprecated.',
            __METHOD__,
        );

        return true;
    }
}

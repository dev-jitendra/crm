<?php

namespace Doctrine\DBAL\Types;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\DB2Platform;
use Doctrine\Deprecations\Deprecation;


class BooleanType extends Type
{
    
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getBooleanTypeDeclarationSQL($column);
    }

    
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $platform->convertBooleansToDatabaseValue($value);
    }

    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $platform->convertFromBoolean($value);
    }

    
    public function getName()
    {
        return Types::BOOLEAN;
    }

    
    public function getBindingType()
    {
        return ParameterType::BOOLEAN;
    }

    
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            '%s is deprecated.',
            __METHOD__,
        );

        
        
        return $platform instanceof DB2Platform;
    }
}

<?php

namespace Doctrine\DBAL\Types;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;


class SmallIntType extends Type implements PhpIntegerMappingType
{
    
    public function getName()
    {
        return Types::SMALLINT;
    }

    
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getSmallIntTypeDeclarationSQL($column);
    }

    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : (int) $value;
    }

    
    public function getBindingType()
    {
        return ParameterType::INTEGER;
    }
}

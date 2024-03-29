<?php

namespace Doctrine\DBAL\Types;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;


class BigIntType extends Type implements PhpIntegerMappingType
{
    
    public function getName()
    {
        return Types::BIGINT;
    }

    
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getBigIntTypeDeclarationSQL($column);
    }

    
    public function getBindingType()
    {
        return ParameterType::STRING;
    }

    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : (string) $value;
    }
}

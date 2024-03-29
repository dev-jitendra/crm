<?php

namespace Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;

use function is_float;
use function is_int;

use const PHP_VERSION_ID;


class DecimalType extends Type
{
    
    public function getName()
    {
        return Types::DECIMAL;
    }

    
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getDecimalTypeDeclarationSQL($column);
    }

    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        
        
        if ((PHP_VERSION_ID >= 80100 || $platform instanceof SqlitePlatform) && (is_float($value) || is_int($value))) {
            return (string) $value;
        }

        return $value;
    }
}

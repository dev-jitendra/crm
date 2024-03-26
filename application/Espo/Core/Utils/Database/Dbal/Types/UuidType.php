<?php


namespace Espo\Core\Utils\Database\Dbal\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;


class UuidType extends Type
{
    public const NAME = 'uuid';

    public function getName()
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return 'UUID';
    }
}

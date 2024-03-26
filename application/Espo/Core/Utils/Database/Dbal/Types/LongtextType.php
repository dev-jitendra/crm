<?php


namespace Espo\Core\Utils\Database\Dbal\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;


class LongtextType extends TextType
{
    public const NAME = 'longtext';

    public function getName()
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return 'LONGTEXT';
    }
}

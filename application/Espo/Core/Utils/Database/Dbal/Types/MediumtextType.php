<?php


namespace Espo\Core\Utils\Database\Dbal\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;


class MediumtextType extends TextType
{
    public const NAME = 'mediumtext';

    public function getName()
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return 'MEDIUMTEXT';
    }
}

<?php

namespace Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Types\Types;
use Doctrine\Deprecations\Deprecation;


class MariaDBPlatform extends MySQLPlatform
{
    
    public function getDefaultValueDeclarationSQL($column)
    {
        return AbstractPlatform::getDefaultValueDeclarationSQL($column);
    }

    
    public function getJsonTypeDeclarationSQL(array $column): string
    {
        return 'LONGTEXT';
    }

    
    protected function getReservedKeywordsClass(): string
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'MariaDb1027Platform::getReservedKeywordsClass() is deprecated,'
                . ' use MariaDb1027Platform::createReservedKeywordsList() instead.',
        );

        return Keywords\MariaDb102Keywords::class;
    }

    protected function initializeDoctrineTypeMappings(): void
    {
        parent::initializeDoctrineTypeMappings();

        $this->doctrineTypeMapping['json'] = Types::JSON;
    }
}

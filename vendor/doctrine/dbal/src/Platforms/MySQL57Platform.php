<?php

namespace Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\SQL\Parser;
use Doctrine\DBAL\Types\Types;
use Doctrine\Deprecations\Deprecation;


class MySQL57Platform extends MySQLPlatform
{
    
    public function hasNativeJsonType()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            '%s is deprecated.',
            __METHOD__,
        );

        return true;
    }

    
    public function getJsonTypeDeclarationSQL(array $column)
    {
        return 'JSON';
    }

    public function createSQLParser(): Parser
    {
        return new Parser(true);
    }

    
    protected function getPreAlterTableRenameIndexForeignKeySQL(TableDiff $diff)
    {
        return [];
    }

    
    protected function getPostAlterTableRenameIndexForeignKeySQL(TableDiff $diff)
    {
        return [];
    }

    
    protected function getRenameIndexSQL($oldIndexName, Index $index, $tableName)
    {
        return ['ALTER TABLE ' . $tableName . ' RENAME INDEX ' . $oldIndexName . ' TO ' . $index->getQuotedName($this)];
    }

    
    protected function getReservedKeywordsClass()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'MySQL57Platform::getReservedKeywordsClass() is deprecated,'
                . ' use MySQL57Platform::createReservedKeywordsList() instead.',
        );

        return Keywords\MySQL57Keywords::class;
    }

    
    protected function initializeDoctrineTypeMappings()
    {
        parent::initializeDoctrineTypeMappings();

        $this->doctrineTypeMapping['json'] = Types::JSON;
    }
}

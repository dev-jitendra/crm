<?php

namespace Doctrine\DBAL\Schema\Visitor;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\Table;
use Doctrine\Deprecations\Deprecation;

use function array_merge;


class CreateSchemaSqlCollector extends AbstractVisitor
{
    
    private array $createNamespaceQueries = [];

    
    private array $createTableQueries = [];

    
    private array $createSequenceQueries = [];

    
    private array $createFkConstraintQueries = [];

    private AbstractPlatform $platform;

    public function __construct(AbstractPlatform $platform)
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'CreateSchemaSqlCollector is deprecated. Use CreateSchemaObjectsSQLBuilder instead.',
        );

        $this->platform = $platform;
    }

    
    public function acceptNamespace($namespaceName)
    {
        if (! $this->platform->supportsSchemas()) {
            return;
        }

        $this->createNamespaceQueries[] = $this->platform->getCreateSchemaSQL($namespaceName);
    }

    
    public function acceptTable(Table $table)
    {
        $this->createTableQueries = array_merge($this->createTableQueries, $this->platform->getCreateTableSQL($table));
    }

    
    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
    {
        if (! $this->platform->supportsForeignKeyConstraints()) {
            return;
        }

        $this->createFkConstraintQueries[] = $this->platform->getCreateForeignKeySQL($fkConstraint, $localTable);
    }

    
    public function acceptSequence(Sequence $sequence)
    {
        $this->createSequenceQueries[] = $this->platform->getCreateSequenceSQL($sequence);
    }

    
    public function resetQueries()
    {
        $this->createNamespaceQueries    = [];
        $this->createTableQueries        = [];
        $this->createSequenceQueries     = [];
        $this->createFkConstraintQueries = [];
    }

    
    public function getQueries()
    {
        return array_merge(
            $this->createNamespaceQueries,
            $this->createSequenceQueries,
            $this->createTableQueries,
            $this->createFkConstraintQueries,
        );
    }
}

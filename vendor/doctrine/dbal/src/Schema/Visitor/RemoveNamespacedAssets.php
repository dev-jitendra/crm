<?php

namespace Doctrine\DBAL\Schema\Visitor;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\Table;
use Doctrine\Deprecations\Deprecation;


class RemoveNamespacedAssets extends AbstractVisitor
{
    private ?Schema $schema = null;

    public function __construct()
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'RemoveNamespacedAssets is deprecated. Do not use namespaces'
                . " if the target database platform doesn't support them.",
        );
    }

    
    public function acceptSchema(Schema $schema)
    {
        $this->schema = $schema;
    }

    
    public function acceptTable(Table $table)
    {
        if ($this->schema === null) {
            return;
        }

        if ($table->isInDefaultNamespace($this->schema->getName())) {
            return;
        }

        $this->schema->dropTable($table->getName());
    }

    
    public function acceptSequence(Sequence $sequence)
    {
        if ($this->schema === null) {
            return;
        }

        if ($sequence->isInDefaultNamespace($this->schema->getName())) {
            return;
        }

        $this->schema->dropSequence($sequence->getName());
    }

    
    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
    {
        if ($this->schema === null) {
            return;
        }

        
        
        
        if (! $this->schema->hasTable($fkConstraint->getForeignTableName())) {
            $localTable->removeForeignKey($fkConstraint->getName());

            return;
        }

        $foreignTable = $this->schema->getTable($fkConstraint->getForeignTableName());
        if ($foreignTable->isInDefaultNamespace($this->schema->getName())) {
            return;
        }

        $localTable->removeForeignKey($fkConstraint->getName());
    }
}

<?php

namespace Doctrine\DBAL\Id;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Visitor\Visitor;
use Doctrine\Deprecations\Deprecation;


class TableGeneratorSchemaVisitor implements Visitor
{
    
    private $generatorTableName;

    
    public function __construct($generatorTableName = 'sequences')
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'The TableGeneratorSchemaVisitor class is is deprecated.',
        );

        $this->generatorTableName = $generatorTableName;
    }

    
    public function acceptSchema(Schema $schema)
    {
        $table = $schema->createTable($this->generatorTableName);
        $table->addColumn('sequence_name', 'string');
        $table->addColumn('sequence_value', 'integer', ['default' => 1]);
        $table->addColumn('sequence_increment_by', 'integer', ['default' => 1]);
    }

    
    public function acceptTable(Table $table)
    {
    }

    
    public function acceptColumn(Table $table, Column $column)
    {
    }

    
    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
    {
    }

    
    public function acceptIndex(Table $table, Index $index)
    {
    }

    
    public function acceptSequence(Sequence $sequence)
    {
    }
}

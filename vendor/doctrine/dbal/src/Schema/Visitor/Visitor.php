<?php

namespace Doctrine\DBAL\Schema\Visitor;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\Table;


interface Visitor
{
    
    public function acceptSchema(Schema $schema);

    
    public function acceptTable(Table $table);

    
    public function acceptColumn(Table $table, Column $column);

    
    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint);

    
    public function acceptIndex(Table $table, Index $index);

    
    public function acceptSequence(Sequence $sequence);
}

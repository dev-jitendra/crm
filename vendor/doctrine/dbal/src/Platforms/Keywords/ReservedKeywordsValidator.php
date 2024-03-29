<?php

namespace Doctrine\DBAL\Platforms\Keywords;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Visitor\Visitor;
use Doctrine\Deprecations\Deprecation;

use function count;
use function implode;
use function str_replace;


class ReservedKeywordsValidator implements Visitor
{
    
    private array $keywordLists;

    
    private array $violations = [];

    
    public function __construct(array $keywordLists)
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'ReservedKeywordsValidator is deprecated. Use database documentation instead.',
        );

        $this->keywordLists = $keywordLists;
    }

    
    public function getViolations()
    {
        return $this->violations;
    }

    
    private function isReservedWord($word): array
    {
        if ($word[0] === '`') {
            $word = str_replace('`', '', $word);
        }

        $keywordLists = [];
        foreach ($this->keywordLists as $keywordList) {
            if (! $keywordList->isKeyword($word)) {
                continue;
            }

            $keywordLists[] = $keywordList->getName();
        }

        return $keywordLists;
    }

    
    private function addViolation($asset, $violatedPlatforms): void
    {
        if (count($violatedPlatforms) === 0) {
            return;
        }

        $this->violations[] = $asset . ' keyword violations: ' . implode(', ', $violatedPlatforms);
    }

    
    public function acceptColumn(Table $table, Column $column)
    {
        $this->addViolation(
            'Table ' . $table->getName() . ' column ' . $column->getName(),
            $this->isReservedWord($column->getName()),
        );
    }

    
    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
    {
    }

    
    public function acceptIndex(Table $table, Index $index)
    {
    }

    
    public function acceptSchema(Schema $schema)
    {
    }

    
    public function acceptSequence(Sequence $sequence)
    {
    }

    
    public function acceptTable(Table $table)
    {
        $this->addViolation(
            'Table ' . $table->getName(),
            $this->isReservedWord($table->getName()),
        );
    }
}

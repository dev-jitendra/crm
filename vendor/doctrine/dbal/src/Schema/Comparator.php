<?php

namespace Doctrine\DBAL\Schema;

use BadMethodCallException;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types;
use Doctrine\Deprecations\Deprecation;

use function array_intersect_key;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_unique;
use function assert;
use function count;
use function get_class;
use function sprintf;
use function strtolower;


class Comparator
{
    private ?AbstractPlatform $platform;

    
    public function __construct(?AbstractPlatform $platform = null)
    {
        if ($platform === null) {
            Deprecation::triggerIfCalledFromOutside(
                'doctrine/dbal',
                'https:
                'Not passing a $platform to %s is deprecated.'
                    . ' Use AbstractSchemaManager::createComparator() to instantiate the comparator.',
                __METHOD__,
            );
        }

        $this->platform = $platform;
    }

    
    public function __call(string $method, array $args): SchemaDiff
    {
        if ($method !== 'compareSchemas') {
            throw new BadMethodCallException(sprintf('Unknown method "%s"', $method));
        }

        return $this->doCompareSchemas(...$args);
    }

    
    public static function __callStatic(string $method, array $args): SchemaDiff
    {
        if ($method !== 'compareSchemas') {
            throw new BadMethodCallException(sprintf('Unknown method "%s"', $method));
        }

        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'Calling %s::%s() statically is deprecated.',
            self::class,
            $method,
        );

        $comparator = new self();

        return $comparator->doCompareSchemas(...$args);
    }

    
    private function doCompareSchemas(
        Schema $fromSchema,
        Schema $toSchema
    ) {
        $createdSchemas   = [];
        $droppedSchemas   = [];
        $createdTables    = [];
        $alteredTables    = [];
        $droppedTables    = [];
        $createdSequences = [];
        $alteredSequences = [];
        $droppedSequences = [];

        $orphanedForeignKeys = [];

        $foreignKeysToTable = [];

        foreach ($toSchema->getNamespaces() as $namespace) {
            if ($fromSchema->hasNamespace($namespace)) {
                continue;
            }

            $createdSchemas[$namespace] = $namespace;
        }

        foreach ($fromSchema->getNamespaces() as $namespace) {
            if ($toSchema->hasNamespace($namespace)) {
                continue;
            }

            $droppedSchemas[$namespace] = $namespace;
        }

        foreach ($toSchema->getTables() as $table) {
            $tableName = $table->getShortestName($toSchema->getName());
            if (! $fromSchema->hasTable($tableName)) {
                $createdTables[$tableName] = $toSchema->getTable($tableName);
            } else {
                $tableDifferences = $this->diffTable(
                    $fromSchema->getTable($tableName),
                    $toSchema->getTable($tableName),
                );

                if ($tableDifferences !== false) {
                    $alteredTables[$tableName] = $tableDifferences;
                }
            }
        }

        
        foreach ($fromSchema->getTables() as $table) {
            $tableName = $table->getShortestName($fromSchema->getName());

            $table = $fromSchema->getTable($tableName);
            if (! $toSchema->hasTable($tableName)) {
                $droppedTables[$tableName] = $table;
            }

            
            foreach ($table->getForeignKeys() as $foreignKey) {
                $foreignTable = strtolower($foreignKey->getForeignTableName());
                if (! isset($foreignKeysToTable[$foreignTable])) {
                    $foreignKeysToTable[$foreignTable] = [];
                }

                $foreignKeysToTable[$foreignTable][] = $foreignKey;
            }
        }

        foreach ($droppedTables as $tableName => $table) {
            if (! isset($foreignKeysToTable[$tableName])) {
                continue;
            }

            foreach ($foreignKeysToTable[$tableName] as $foreignKey) {
                if (isset($droppedTables[strtolower($foreignKey->getLocalTableName())])) {
                    continue;
                }

                $orphanedForeignKeys[] = $foreignKey;
            }

            
            
            foreach ($foreignKeysToTable[$tableName] as $foreignKey) {
                
                $localTableName = strtolower($foreignKey->getLocalTableName());
                if (! isset($alteredTables[$localTableName])) {
                    continue;
                }

                foreach ($alteredTables[$localTableName]->getDroppedForeignKeys() as $droppedForeignKey) {
                    assert($droppedForeignKey instanceof ForeignKeyConstraint);

                    
                    if ($tableName !== strtolower($droppedForeignKey->getForeignTableName())) {
                        continue;
                    }

                    $alteredTables[$localTableName]->unsetDroppedForeignKey($droppedForeignKey);
                }
            }
        }

        foreach ($toSchema->getSequences() as $sequence) {
            $sequenceName = $sequence->getShortestName($toSchema->getName());
            if (! $fromSchema->hasSequence($sequenceName)) {
                if (! $this->isAutoIncrementSequenceInSchema($fromSchema, $sequence)) {
                    $createdSequences[] = $sequence;
                }
            } else {
                if ($this->diffSequence($sequence, $fromSchema->getSequence($sequenceName))) {
                    $alteredSequences[] = $toSchema->getSequence($sequenceName);
                }
            }
        }

        foreach ($fromSchema->getSequences() as $sequence) {
            if ($this->isAutoIncrementSequenceInSchema($toSchema, $sequence)) {
                continue;
            }

            $sequenceName = $sequence->getShortestName($fromSchema->getName());

            if ($toSchema->hasSequence($sequenceName)) {
                continue;
            }

            $droppedSequences[] = $sequence;
        }

        $diff = new SchemaDiff(
            $createdTables,
            $alteredTables,
            $droppedTables,
            $fromSchema,
            $createdSchemas,
            $droppedSchemas,
            $createdSequences,
            $alteredSequences,
            $droppedSequences,
        );

        $diff->orphanedForeignKeys = $orphanedForeignKeys;

        return $diff;
    }

    
    public function compare(Schema $fromSchema, Schema $toSchema)
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'Method compare() is deprecated. Use a non-static call to compareSchemas() instead.',
        );

        return $this->compareSchemas($fromSchema, $toSchema);
    }

    
    private function isAutoIncrementSequenceInSchema($schema, $sequence): bool
    {
        foreach ($schema->getTables() as $table) {
            if ($sequence->isAutoIncrementsFor($table)) {
                return true;
            }
        }

        return false;
    }

    
    public function diffSequence(Sequence $sequence1, Sequence $sequence2)
    {
        if ($sequence1->getAllocationSize() !== $sequence2->getAllocationSize()) {
            return true;
        }

        return $sequence1->getInitialValue() !== $sequence2->getInitialValue();
    }

    
    public function diffTable(Table $fromTable, Table $toTable)
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            '%s is deprecated. Use compareTables() instead.',
            __METHOD__,
        );

        $diff = $this->compareTables($fromTable, $toTable);

        if ($diff->isEmpty()) {
            return false;
        }

        return $diff;
    }

    
    public function compareTables(Table $fromTable, Table $toTable): TableDiff
    {
        $addedColumns        = [];
        $modifiedColumns     = [];
        $droppedColumns      = [];
        $addedIndexes        = [];
        $modifiedIndexes     = [];
        $droppedIndexes      = [];
        $addedForeignKeys    = [];
        $modifiedForeignKeys = [];
        $droppedForeignKeys  = [];

        $fromTableColumns = $fromTable->getColumns();
        $toTableColumns   = $toTable->getColumns();

        
        foreach ($toTableColumns as $columnName => $column) {
            if ($fromTable->hasColumn($columnName)) {
                continue;
            }

            $addedColumns[$columnName] = $column;
        }

        
        foreach ($fromTableColumns as $columnName => $column) {
            
            if (! $toTable->hasColumn($columnName)) {
                $droppedColumns[$columnName] = $column;

                continue;
            }

            $toColumn = $toTable->getColumn($columnName);

            
            $changedProperties = $this->diffColumn($column, $toColumn);

            if ($this->platform !== null) {
                if ($this->columnsEqual($column, $toColumn)) {
                    continue;
                }
            } elseif (count($changedProperties) === 0) {
                continue;
            }

            $modifiedColumns[$column->getName()] = new ColumnDiff(
                $column->getName(),
                $toColumn,
                $changedProperties,
                $column,
            );
        }

        $renamedColumns = $this->detectRenamedColumns($addedColumns, $droppedColumns);

        $fromTableIndexes = $fromTable->getIndexes();
        $toTableIndexes   = $toTable->getIndexes();

        
        foreach ($toTableIndexes as $indexName => $index) {
            if (($index->isPrimary() && $fromTable->getPrimaryKey() !== null) || $fromTable->hasIndex($indexName)) {
                continue;
            }

            $addedIndexes[$indexName] = $index;
        }

        
        foreach ($fromTableIndexes as $indexName => $index) {
            
            if (
                ($index->isPrimary() && $toTable->getPrimaryKey() === null) ||
                ! $index->isPrimary() && ! $toTable->hasIndex($indexName)
            ) {
                $droppedIndexes[$indexName] = $index;

                continue;
            }

            
            $toTableIndex = $index->isPrimary() ? $toTable->getPrimaryKey() : $toTable->getIndex($indexName);
            assert($toTableIndex instanceof Index);

            if (! $this->diffIndex($index, $toTableIndex)) {
                continue;
            }

            $modifiedIndexes[$indexName] = $toTableIndex;
        }

        $renamedIndexes = $this->detectRenamedIndexes($addedIndexes, $droppedIndexes);

        $fromForeignKeys = $fromTable->getForeignKeys();
        $toForeignKeys   = $toTable->getForeignKeys();

        foreach ($fromForeignKeys as $fromKey => $fromConstraint) {
            foreach ($toForeignKeys as $toKey => $toConstraint) {
                if ($this->diffForeignKey($fromConstraint, $toConstraint) === false) {
                    unset($fromForeignKeys[$fromKey], $toForeignKeys[$toKey]);
                } else {
                    if (strtolower($fromConstraint->getName()) === strtolower($toConstraint->getName())) {
                        $modifiedForeignKeys[] = $toConstraint;

                        unset($fromForeignKeys[$fromKey], $toForeignKeys[$toKey]);
                    }
                }
            }
        }

        foreach ($fromForeignKeys as $fromConstraint) {
            $droppedForeignKeys[] = $fromConstraint;
        }

        foreach ($toForeignKeys as $toConstraint) {
            $addedForeignKeys[] = $toConstraint;
        }

        return new TableDiff(
            $toTable->getName(),
            $addedColumns,
            $modifiedColumns,
            $droppedColumns,
            $addedIndexes,
            $modifiedIndexes,
            $droppedIndexes,
            $fromTable,
            $addedForeignKeys,
            $modifiedForeignKeys,
            $droppedForeignKeys,
            $renamedColumns,
            $renamedIndexes,
        );
    }

    
    private function detectRenamedColumns(array &$addedColumns, array &$removedColumns): array
    {
        $candidatesByName = [];

        foreach ($addedColumns as $addedColumnName => $addedColumn) {
            foreach ($removedColumns as $removedColumn) {
                if (! $this->columnsEqual($addedColumn, $removedColumn)) {
                    continue;
                }

                $candidatesByName[$addedColumn->getName()][] = [$removedColumn, $addedColumn, $addedColumnName];
            }
        }

        $renamedColumns = [];

        foreach ($candidatesByName as $candidates) {
            if (count($candidates) !== 1) {
                continue;
            }

            [$removedColumn, $addedColumn] = $candidates[0];
            $removedColumnName             = $removedColumn->getName();
            $addedColumnName               = strtolower($addedColumn->getName());

            if (isset($renamedColumns[$removedColumnName])) {
                continue;
            }

            $renamedColumns[$removedColumnName] = $addedColumn;
            unset(
                $addedColumns[$addedColumnName],
                $removedColumns[strtolower($removedColumnName)],
            );
        }

        return $renamedColumns;
    }

    
    private function detectRenamedIndexes(array &$addedIndexes, array &$removedIndexes): array
    {
        $candidatesByName = [];

        
        foreach ($addedIndexes as $addedIndexName => $addedIndex) {
            foreach ($removedIndexes as $removedIndex) {
                if ($this->diffIndex($addedIndex, $removedIndex)) {
                    continue;
                }

                $candidatesByName[$addedIndex->getName()][] = [$removedIndex, $addedIndex, $addedIndexName];
            }
        }

        $renamedIndexes = [];

        foreach ($candidatesByName as $candidates) {
            
            
            
            
            if (count($candidates) !== 1) {
                continue;
            }

            [$removedIndex, $addedIndex] = $candidates[0];

            $removedIndexName = strtolower($removedIndex->getName());
            $addedIndexName   = strtolower($addedIndex->getName());

            if (isset($renamedIndexes[$removedIndexName])) {
                continue;
            }

            $renamedIndexes[$removedIndexName] = $addedIndex;
            unset(
                $addedIndexes[$addedIndexName],
                $removedIndexes[$removedIndexName],
            );
        }

        return $renamedIndexes;
    }

    
    public function diffForeignKey(ForeignKeyConstraint $key1, ForeignKeyConstraint $key2)
    {
        if (
            array_map('strtolower', $key1->getUnquotedLocalColumns())
            !== array_map('strtolower', $key2->getUnquotedLocalColumns())
        ) {
            return true;
        }

        if (
            array_map('strtolower', $key1->getUnquotedForeignColumns())
            !== array_map('strtolower', $key2->getUnquotedForeignColumns())
        ) {
            return true;
        }

        if ($key1->getUnqualifiedForeignTableName() !== $key2->getUnqualifiedForeignTableName()) {
            return true;
        }

        if ($key1->onUpdate() !== $key2->onUpdate()) {
            return true;
        }

        return $key1->onDelete() !== $key2->onDelete();
    }

    
    public function columnsEqual(Column $column1, Column $column2): bool
    {
        if ($this->platform === null) {
            return $this->diffColumn($column1, $column2) === [];
        }

        return $this->platform->columnsEqual($column1, $column2);
    }

    
    public function diffColumn(Column $column1, Column $column2)
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            '%s is deprecated. Use diffTable() instead.',
            __METHOD__,
        );

        $properties1 = $column1->toArray();
        $properties2 = $column2->toArray();

        $changedProperties = [];

        if (get_class($properties1['type']) !== get_class($properties2['type'])) {
            $changedProperties[] = 'type';
        }

        foreach (['notnull', 'unsigned', 'autoincrement'] as $property) {
            if ($properties1[$property] === $properties2[$property]) {
                continue;
            }

            $changedProperties[] = $property;
        }

        
        
        if (
            ($properties1['default'] === null) !== ($properties2['default'] === null)
            || $properties1['default'] != $properties2['default']
        ) {
            $changedProperties[] = 'default';
        }

        if (
            ($properties1['type'] instanceof Types\StringType && ! $properties1['type'] instanceof Types\GuidType) ||
            $properties1['type'] instanceof Types\BinaryType
        ) {
            
            $length1 = $properties1['length'] ?? 255;
            $length2 = $properties2['length'] ?? 255;
            if ($length1 !== $length2) {
                $changedProperties[] = 'length';
            }

            if ($properties1['fixed'] !== $properties2['fixed']) {
                $changedProperties[] = 'fixed';
            }
        } elseif ($properties1['type'] instanceof Types\DecimalType) {
            if (($properties1['precision'] ?? 10) !== ($properties2['precision'] ?? 10)) {
                $changedProperties[] = 'precision';
            }

            if ($properties1['scale'] !== $properties2['scale']) {
                $changedProperties[] = 'scale';
            }
        }

        
        if (
            $properties1['comment'] !== $properties2['comment'] &&
            ! ($properties1['comment'] === null && $properties2['comment'] === '') &&
            ! ($properties2['comment'] === null && $properties1['comment'] === '')
        ) {
            $changedProperties[] = 'comment';
        }

        $customOptions1 = $column1->getCustomSchemaOptions();
        $customOptions2 = $column2->getCustomSchemaOptions();

        foreach (array_merge(array_keys($customOptions1), array_keys($customOptions2)) as $key) {
            if (! array_key_exists($key, $properties1) || ! array_key_exists($key, $properties2)) {
                $changedProperties[] = $key;
            } elseif ($properties1[$key] !== $properties2[$key]) {
                $changedProperties[] = $key;
            }
        }

        $platformOptions1 = $column1->getPlatformOptions();
        $platformOptions2 = $column2->getPlatformOptions();

        foreach (array_keys(array_intersect_key($platformOptions1, $platformOptions2)) as $key) {
            if ($properties1[$key] === $properties2[$key]) {
                continue;
            }

            $changedProperties[] = $key;
        }

        return array_unique($changedProperties);
    }

    
    public function diffIndex(Index $index1, Index $index2)
    {
        return ! ($index1->isFulfilledBy($index2) && $index2->isFulfilledBy($index1));
    }
}

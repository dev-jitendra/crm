<?php

namespace Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\InvalidLockMode;
use Doctrine\DBAL\LockMode;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Identifier;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\SQLServerSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\Deprecations\Deprecation;
use InvalidArgumentException;

use function array_merge;
use function array_unique;
use function array_values;
use function count;
use function crc32;
use function dechex;
use function explode;
use function func_get_arg;
use function func_get_args;
use function func_num_args;
use function implode;
use function is_array;
use function is_bool;
use function is_numeric;
use function is_string;
use function preg_match;
use function preg_match_all;
use function sprintf;
use function str_replace;
use function strpos;
use function strtoupper;
use function substr_count;

use const PREG_OFFSET_CAPTURE;


class SQLServerPlatform extends AbstractPlatform
{
    
    public function getCurrentDateSQL()
    {
        return $this->getConvertExpression('date', 'GETDATE()');
    }

    
    public function getCurrentTimeSQL()
    {
        return $this->getConvertExpression('time', 'GETDATE()');
    }

    
    private function getConvertExpression($dataType, $expression): string
    {
        return sprintf('CONVERT(%s, %s)', $dataType, $expression);
    }

    
    protected function getDateArithmeticIntervalExpression($date, $operator, $interval, $unit)
    {
        $factorClause = '';

        if ($operator === '-') {
            $factorClause = '-1 * ';
        }

        return 'DATEADD(' . $unit . ', ' . $factorClause . $interval . ', ' . $date . ')';
    }

    
    public function getDateDiffExpression($date1, $date2)
    {
        return 'DATEDIFF(day, ' . $date2 . ',' . $date1 . ')';
    }

    
    public function prefersIdentityColumns()
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'SQLServerPlatform::prefersIdentityColumns() is deprecated.',
        );

        return true;
    }

    
    public function supportsIdentityColumns()
    {
        return true;
    }

    
    public function supportsReleaseSavepoints()
    {
        return false;
    }

    
    public function supportsSchemas()
    {
        return true;
    }

    
    public function getDefaultSchemaName()
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            '%s is deprecated.',
            __METHOD__,
        );

        return 'dbo';
    }

    
    public function supportsColumnCollation()
    {
        return true;
    }

    public function supportsSequences(): bool
    {
        return true;
    }

    public function getAlterSequenceSQL(Sequence $sequence): string
    {
        return 'ALTER SEQUENCE ' . $sequence->getQuotedName($this) .
            ' INCREMENT BY ' . $sequence->getAllocationSize();
    }

    public function getCreateSequenceSQL(Sequence $sequence): string
    {
        return 'CREATE SEQUENCE ' . $sequence->getQuotedName($this) .
            ' START WITH ' . $sequence->getInitialValue() .
            ' INCREMENT BY ' . $sequence->getAllocationSize() .
            ' MINVALUE ' . $sequence->getInitialValue();
    }

    
    public function getListSequencesSQL($database)
    {
        return 'SELECT seq.name,
                       CAST(
                           seq.increment AS VARCHAR(MAX)
                       ) AS increment, -- CAST avoids driver error for sql_variant type
                       CAST(
                           seq.start_value AS VARCHAR(MAX)
                       ) AS start_value -- CAST avoids driver error for sql_variant type
                FROM   sys.sequences AS seq';
    }

    
    public function getSequenceNextValSQL($sequence)
    {
        return 'SELECT NEXT VALUE FOR ' . $sequence;
    }

    
    public function hasNativeGuidType()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            '%s is deprecated.',
            __METHOD__,
        );

        return true;
    }

    
    public function getDropForeignKeySQL($foreignKey, $table)
    {
        if (! $foreignKey instanceof ForeignKeyConstraint) {
            $foreignKey = new Identifier($foreignKey);
        }

        if (! $table instanceof Table) {
            $table = new Identifier($table);
        }

        $foreignKey = $foreignKey->getQuotedName($this);
        $table      = $table->getQuotedName($this);

        return 'ALTER TABLE ' . $table . ' DROP CONSTRAINT ' . $foreignKey;
    }

    
    public function getDropIndexSQL($index, $table = null)
    {
        if ($index instanceof Index) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Passing $index as an Index object to %s is deprecated. Pass it as a quoted name instead.',
                __METHOD__,
            );

            $index = $index->getQuotedName($this);
        } elseif (! is_string($index)) {
            throw new InvalidArgumentException(
                __METHOD__ . '() expects $index parameter to be string or ' . Index::class . '.',
            );
        }

        if ($table instanceof Table) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Passing $table as an Table object to %s is deprecated. Pass it as a quoted name instead.',
                __METHOD__,
            );

            $table = $table->getQuotedName($this);
        } elseif (! is_string($table)) {
            throw new InvalidArgumentException(
                __METHOD__ . '() expects $table parameter to be string or ' . Table::class . '.',
            );
        }

        return 'DROP INDEX ' . $index . ' ON ' . $table;
    }

    
    protected function _getCreateTableSQL($name, array $columns, array $options = [])
    {
        $defaultConstraintsSql = [];
        $commentsSql           = [];

        $tableComment = $options['comment'] ?? null;
        if ($tableComment !== null) {
            $commentsSql[] = $this->getCommentOnTableSQL($name, $tableComment);
        }

        
        
        foreach ($columns as &$column) {
            if (! empty($column['primary'])) {
                $column['notnull'] = true;
            }

            
            if (isset($column['default'])) {
                $defaultConstraintsSql[] = 'ALTER TABLE ' . $name .
                    ' ADD' . $this->getDefaultConstraintDeclarationSQL($name, $column);
            }

            if (empty($column['comment']) && ! is_numeric($column['comment'])) {
                continue;
            }

            $commentsSql[] = $this->getCreateColumnCommentSQL($name, $column['name'], $column['comment']);
        }

        $columnListSql = $this->getColumnDeclarationListSQL($columns);

        if (isset($options['uniqueConstraints']) && ! empty($options['uniqueConstraints'])) {
            foreach ($options['uniqueConstraints'] as $constraintName => $definition) {
                $columnListSql .= ', ' . $this->getUniqueConstraintDeclarationSQL($constraintName, $definition);
            }
        }

        if (isset($options['primary']) && ! empty($options['primary'])) {
            $flags = '';
            if (isset($options['primary_index']) && $options['primary_index']->hasFlag('nonclustered')) {
                $flags = ' NONCLUSTERED';
            }

            $columnListSql .= ', PRIMARY KEY' . $flags
                . ' (' . implode(', ', array_unique(array_values($options['primary']))) . ')';
        }

        $query = 'CREATE TABLE ' . $name . ' (' . $columnListSql;

        $check = $this->getCheckDeclarationSQL($columns);
        if (! empty($check)) {
            $query .= ', ' . $check;
        }

        $query .= ')';

        $sql = [$query];

        if (isset($options['indexes']) && ! empty($options['indexes'])) {
            foreach ($options['indexes'] as $index) {
                $sql[] = $this->getCreateIndexSQL($index, $name);
            }
        }

        if (isset($options['foreignKeys'])) {
            foreach ($options['foreignKeys'] as $definition) {
                $sql[] = $this->getCreateForeignKeySQL($definition, $name);
            }
        }

        return array_merge($sql, $commentsSql, $defaultConstraintsSql);
    }

    
    public function getCreatePrimaryKeySQL(Index $index, $table)
    {
        if ($table instanceof Table) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.',
                __METHOD__,
            );

            $identifier = $table->getQuotedName($this);
        } else {
            $identifier = $table;
        }

        $sql = 'ALTER TABLE ' . $identifier . ' ADD PRIMARY KEY';

        if ($index->hasFlag('nonclustered')) {
            $sql .= ' NONCLUSTERED';
        }

        return $sql . ' (' . $this->getIndexFieldDeclarationListSQL($index) . ')';
    }

    
    protected function getCreateColumnCommentSQL($tableName, $columnName, $comment)
    {
        if (strpos($tableName, '.') !== false) {
            [$schemaSQL, $tableSQL] = explode('.', $tableName);
            $schemaSQL              = $this->quoteStringLiteral($schemaSQL);
            $tableSQL               = $this->quoteStringLiteral($tableSQL);
        } else {
            $schemaSQL = "'dbo'";
            $tableSQL  = $this->quoteStringLiteral($tableName);
        }

        return $this->getAddExtendedPropertySQL(
            'MS_Description',
            $comment,
            'SCHEMA',
            $schemaSQL,
            'TABLE',
            $tableSQL,
            'COLUMN',
            $columnName,
        );
    }

    
    public function getDefaultConstraintDeclarationSQL($table, array $column)
    {
        if (! isset($column['default'])) {
            throw new InvalidArgumentException("Incomplete column definition. 'default' required.");
        }

        $columnName = new Identifier($column['name']);

        return ' CONSTRAINT ' .
            $this->generateDefaultConstraintName($table, $column['name']) .
            $this->getDefaultValueDeclarationSQL($column) .
            ' FOR ' . $columnName->getQuotedName($this);
    }

    
    public function getCreateIndexSQL(Index $index, $table)
    {
        $constraint = parent::getCreateIndexSQL($index, $table);

        if ($index->isUnique() && ! $index->isPrimary()) {
            $constraint = $this->_appendUniqueConstraintDefinition($constraint, $index);
        }

        return $constraint;
    }

    
    protected function getCreateIndexSQLFlags(Index $index)
    {
        $type = '';
        if ($index->isUnique()) {
            $type .= 'UNIQUE ';
        }

        if ($index->hasFlag('clustered')) {
            $type .= 'CLUSTERED ';
        } elseif ($index->hasFlag('nonclustered')) {
            $type .= 'NONCLUSTERED ';
        }

        return $type;
    }

    
    private function _appendUniqueConstraintDefinition($sql, Index $index): string
    {
        $fields = [];

        foreach ($index->getQuotedColumns($this) as $field) {
            $fields[] = $field . ' IS NOT NULL';
        }

        return $sql . ' WHERE ' . implode(' AND ', $fields);
    }

    
    public function getAlterTableSQL(TableDiff $diff)
    {
        $queryParts  = [];
        $sql         = [];
        $columnSql   = [];
        $commentsSql = [];

        $table = $diff->getOldTable() ?? $diff->getName($this);

        $tableName = $table->getName();

        foreach ($diff->getAddedColumns() as $column) {
            if ($this->onSchemaAlterTableAddColumn($column, $diff, $columnSql)) {
                continue;
            }

            $columnProperties = $column->toArray();

            $addColumnSql = 'ADD ' . $this->getColumnDeclarationSQL($column->getQuotedName($this), $columnProperties);

            if (isset($columnProperties['default'])) {
                $addColumnSql .= ' CONSTRAINT ' . $this->generateDefaultConstraintName(
                    $tableName,
                    $column->getQuotedName($this),
                ) . $this->getDefaultValueDeclarationSQL($columnProperties);
            }

            $queryParts[] = $addColumnSql;

            $comment = $this->getColumnComment($column);

            if (empty($comment) && ! is_numeric($comment)) {
                continue;
            }

            $commentsSql[] = $this->getCreateColumnCommentSQL(
                $tableName,
                $column->getQuotedName($this),
                $comment,
            );
        }

        foreach ($diff->getDroppedColumns() as $column) {
            if ($this->onSchemaAlterTableRemoveColumn($column, $diff, $columnSql)) {
                continue;
            }

            $queryParts[] = 'DROP COLUMN ' . $column->getQuotedName($this);
        }

        foreach ($diff->getModifiedColumns() as $columnDiff) {
            if ($this->onSchemaAlterTableChangeColumn($columnDiff, $diff, $columnSql)) {
                continue;
            }

            $newColumn     = $columnDiff->getNewColumn();
            $newComment    = $this->getColumnComment($newColumn);
            $hasNewComment = ! empty($newComment) || is_numeric($newComment);

            $oldColumn = $columnDiff->getOldColumn();

            if ($oldColumn instanceof Column) {
                $oldComment    = $this->getColumnComment($oldColumn);
                $hasOldComment = ! empty($oldComment) || is_numeric($oldComment);

                if ($hasOldComment && $hasNewComment && $oldComment !== $newComment) {
                    $commentsSql[] = $this->getAlterColumnCommentSQL(
                        $tableName,
                        $newColumn->getQuotedName($this),
                        $newComment,
                    );
                } elseif ($hasOldComment && ! $hasNewComment) {
                    $commentsSql[] = $this->getDropColumnCommentSQL(
                        $tableName,
                        $newColumn->getQuotedName($this),
                    );
                } elseif (! $hasOldComment && $hasNewComment) {
                    $commentsSql[] = $this->getCreateColumnCommentSQL(
                        $tableName,
                        $newColumn->getQuotedName($this),
                        $newComment,
                    );
                }
            }

            
            if ($columnDiff->hasCommentChanged() && count($columnDiff->changedProperties) === 1) {
                continue;
            }

            $requireDropDefaultConstraint = $this->alterColumnRequiresDropDefaultConstraint($columnDiff);

            if ($requireDropDefaultConstraint) {
                $oldColumn = $columnDiff->getOldColumn();

                if ($oldColumn !== null) {
                    $oldColumnName = $oldColumn->getName();
                } else {
                    $oldColumnName = $columnDiff->oldColumnName;
                }

                $queryParts[] = $this->getAlterTableDropDefaultConstraintClause($tableName, $oldColumnName);
            }

            $columnProperties = $newColumn->toArray();

            $queryParts[] = 'ALTER COLUMN ' .
                    $this->getColumnDeclarationSQL($newColumn->getQuotedName($this), $columnProperties);

            if (
                ! isset($columnProperties['default'])
                || (! $requireDropDefaultConstraint && ! $columnDiff->hasDefaultChanged())
            ) {
                continue;
            }

            $queryParts[] = $this->getAlterTableAddDefaultConstraintClause($tableName, $newColumn);
        }

        $tableNameSQL = $table->getQuotedName($this);

        foreach ($diff->getRenamedColumns() as $oldColumnName => $newColumn) {
            if ($this->onSchemaAlterTableRenameColumn($oldColumnName, $newColumn, $diff, $columnSql)) {
                continue;
            }

            $oldColumnName = new Identifier($oldColumnName);

            $sql[] = "sp_rename '" . $tableNameSQL . '.' . $oldColumnName->getQuotedName($this) .
                "', '" . $newColumn->getQuotedName($this) . "', 'COLUMN'";

            
            if ($newColumn->getDefault() === null) {
                continue;
            }

            $queryParts[] = $this->getAlterTableDropDefaultConstraintClause(
                $tableName,
                $oldColumnName->getQuotedName($this),
            );
            $queryParts[] = $this->getAlterTableAddDefaultConstraintClause($tableName, $newColumn);
        }

        $tableSql = [];

        if ($this->onSchemaAlterTable($diff, $tableSql)) {
            return array_merge($tableSql, $columnSql);
        }

        foreach ($queryParts as $query) {
            $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' ' . $query;
        }

        $sql = array_merge($sql, $commentsSql);

        $newName = $diff->getNewName();

        if ($newName !== false) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Generation of "rename table" SQL using %s is deprecated. Use getRenameTableSQL() instead.',
                __METHOD__,
            );

            $sql = array_merge($sql, $this->getRenameTableSQL($tableName, $newName->getName()));
        }

        $sql = array_merge(
            $this->getPreAlterTableIndexForeignKeySQL($diff),
            $sql,
            $this->getPostAlterTableIndexForeignKeySQL($diff),
        );

        return array_merge($sql, $tableSql, $columnSql);
    }

    
    public function getRenameTableSQL(string $oldName, string $newName): array
    {
        return [
            sprintf('sp_rename %s, %s', $this->quoteStringLiteral($oldName), $this->quoteStringLiteral($newName)),

            
            sprintf(
                <<<'SQL'
                DECLARE @sql NVARCHAR(MAX) = N'';
                SELECT @sql += N'EXEC sp_rename N''' + dc.name + ''', N'''
                    + REPLACE(dc.name, '%s', '%s') + ''', ''OBJECT'';'
                    FROM sys.default_constraints dc
                    JOIN sys.tables tbl
                        ON dc.parent_object_id = tbl.object_id
                    WHERE tbl.name = %s;
                EXEC sp_executesql @sql
                SQL,
                $this->generateIdentifierName($oldName),
                $this->generateIdentifierName($newName),
                $this->quoteStringLiteral($newName),
            ),
        ];
    }

    
    private function getAlterTableAddDefaultConstraintClause($tableName, Column $column): string
    {
        $columnDef         = $column->toArray();
        $columnDef['name'] = $column->getQuotedName($this);

        return 'ADD' . $this->getDefaultConstraintDeclarationSQL($tableName, $columnDef);
    }

    
    private function getAlterTableDropDefaultConstraintClause($tableName, $columnName): string
    {
        return 'DROP CONSTRAINT ' . $this->generateDefaultConstraintName($tableName, $columnName);
    }

    
    private function alterColumnRequiresDropDefaultConstraint(ColumnDiff $columnDiff): bool
    {
        $oldColumn = $columnDiff->getOldColumn();

        
        
        if (! $oldColumn instanceof Column) {
            return false;
        }

        
        
        if ($oldColumn->getDefault() === null) {
            return false;
        }

        
        
        if ($columnDiff->hasDefaultChanged()) {
            return true;
        }

        
        
        return $columnDiff->hasTypeChanged() || $columnDiff->hasFixedChanged();
    }

    
    protected function getAlterColumnCommentSQL($tableName, $columnName, $comment)
    {
        if (strpos($tableName, '.') !== false) {
            [$schemaSQL, $tableSQL] = explode('.', $tableName);
            $schemaSQL              = $this->quoteStringLiteral($schemaSQL);
            $tableSQL               = $this->quoteStringLiteral($tableSQL);
        } else {
            $schemaSQL = "'dbo'";
            $tableSQL  = $this->quoteStringLiteral($tableName);
        }

        return $this->getUpdateExtendedPropertySQL(
            'MS_Description',
            $comment,
            'SCHEMA',
            $schemaSQL,
            'TABLE',
            $tableSQL,
            'COLUMN',
            $columnName,
        );
    }

    
    protected function getDropColumnCommentSQL($tableName, $columnName)
    {
        if (strpos($tableName, '.') !== false) {
            [$schemaSQL, $tableSQL] = explode('.', $tableName);
            $schemaSQL              = $this->quoteStringLiteral($schemaSQL);
            $tableSQL               = $this->quoteStringLiteral($tableSQL);
        } else {
            $schemaSQL = "'dbo'";
            $tableSQL  = $this->quoteStringLiteral($tableName);
        }

        return $this->getDropExtendedPropertySQL(
            'MS_Description',
            'SCHEMA',
            $schemaSQL,
            'TABLE',
            $tableSQL,
            'COLUMN',
            $columnName,
        );
    }

    
    protected function getRenameIndexSQL($oldIndexName, Index $index, $tableName)
    {
        return [sprintf(
            "EXEC sp_rename N'%s.%s', N'%s', N'INDEX'",
            $tableName,
            $oldIndexName,
            $index->getQuotedName($this),
        ),
        ];
    }

    
    public function getAddExtendedPropertySQL(
        $name,
        $value = null,
        $level0Type = null,
        $level0Name = null,
        $level1Type = null,
        $level1Name = null,
        $level2Type = null,
        $level2Name = null
    ) {
        return 'EXEC sp_addextendedproperty ' .
            'N' . $this->quoteStringLiteral($name) . ', N' . $this->quoteStringLiteral((string) $value) . ', ' .
            'N' . $this->quoteStringLiteral((string) $level0Type) . ', ' . $level0Name . ', ' .
            'N' . $this->quoteStringLiteral((string) $level1Type) . ', ' . $level1Name . ', ' .
            'N' . $this->quoteStringLiteral((string) $level2Type) . ', ' . $level2Name;
    }

    
    public function getDropExtendedPropertySQL(
        $name,
        $level0Type = null,
        $level0Name = null,
        $level1Type = null,
        $level1Name = null,
        $level2Type = null,
        $level2Name = null
    ) {
        return 'EXEC sp_dropextendedproperty ' .
            'N' . $this->quoteStringLiteral($name) . ', ' .
            'N' . $this->quoteStringLiteral((string) $level0Type) . ', ' . $level0Name . ', ' .
            'N' . $this->quoteStringLiteral((string) $level1Type) . ', ' . $level1Name . ', ' .
            'N' . $this->quoteStringLiteral((string) $level2Type) . ', ' . $level2Name;
    }

    
    public function getUpdateExtendedPropertySQL(
        $name,
        $value = null,
        $level0Type = null,
        $level0Name = null,
        $level1Type = null,
        $level1Name = null,
        $level2Type = null,
        $level2Name = null
    ) {
        return 'EXEC sp_updateextendedproperty ' .
            'N' . $this->quoteStringLiteral($name) . ', N' . $this->quoteStringLiteral((string) $value) . ', ' .
            'N' . $this->quoteStringLiteral((string) $level0Type) . ', ' . $level0Name . ', ' .
            'N' . $this->quoteStringLiteral((string) $level1Type) . ', ' . $level1Name . ', ' .
            'N' . $this->quoteStringLiteral((string) $level2Type) . ', ' . $level2Name;
    }

    
    public function getEmptyIdentityInsertSQL($quotedTableName, $quotedIdentifierColumnName)
    {
        return 'INSERT INTO ' . $quotedTableName . ' DEFAULT VALUES';
    }

    
    public function getListTablesSQL()
    {
        
        
        return 'SELECT name, SCHEMA_NAME (uid) AS schema_name FROM sysobjects'
            . " WHERE type = 'U' AND name != 'sysdiagrams' AND category != 2 ORDER BY name";
    }

    
    public function getListTableColumnsSQL($table, $database = null)
    {
        return "SELECT    col.name,
                          type.name AS type,
                          col.max_length AS length,
                          ~col.is_nullable AS notnull,
                          def.definition AS [default],
                          col.scale,
                          col.precision,
                          col.is_identity AS autoincrement,
                          col.collation_name AS collation,
                          CAST(prop.value AS NVARCHAR(MAX)) AS comment -- CAST avoids driver error for sql_variant type
                FROM      sys.columns AS col
                JOIN      sys.types AS type
                ON        col.user_type_id = type.user_type_id
                JOIN      sys.objects AS obj
                ON        col.object_id = obj.object_id
                JOIN      sys.schemas AS scm
                ON        obj.schema_id = scm.schema_id
                LEFT JOIN sys.default_constraints def
                ON        col.default_object_id = def.object_id
                AND       col.object_id = def.parent_object_id
                LEFT JOIN sys.extended_properties AS prop
                ON        obj.object_id = prop.major_id
                AND       col.column_id = prop.minor_id
                AND       prop.name = 'MS_Description'
                WHERE     obj.type = 'U'
                AND       " . $this->getTableWhereClause($table, 'scm.name', 'obj.name');
    }

    
    public function getListTableForeignKeysSQL($table, $database = null)
    {
        return 'SELECT f.name AS ForeignKey,
                SCHEMA_NAME (f.SCHEMA_ID) AS SchemaName,
                OBJECT_NAME (f.parent_object_id) AS TableName,
                COL_NAME (fc.parent_object_id,fc.parent_column_id) AS ColumnName,
                SCHEMA_NAME (o.SCHEMA_ID) ReferenceSchemaName,
                OBJECT_NAME (f.referenced_object_id) AS ReferenceTableName,
                COL_NAME(fc.referenced_object_id,fc.referenced_column_id) AS ReferenceColumnName,
                f.delete_referential_action_desc,
                f.update_referential_action_desc
                FROM sys.foreign_keys AS f
                INNER JOIN sys.foreign_key_columns AS fc
                INNER JOIN sys.objects AS o ON o.OBJECT_ID = fc.referenced_object_id
                ON f.OBJECT_ID = fc.constraint_object_id
                WHERE ' .
                $this->getTableWhereClause($table, 'SCHEMA_NAME (f.schema_id)', 'OBJECT_NAME (f.parent_object_id)') .
                ' ORDER BY fc.constraint_column_id';
    }

    
    public function getListTableIndexesSQL($table, $database = null)
    {
        return "SELECT idx.name AS key_name,
                       col.name AS column_name,
                       ~idx.is_unique AS non_unique,
                       idx.is_primary_key AS [primary],
                       CASE idx.type
                           WHEN '1' THEN 'clustered'
                           WHEN '2' THEN 'nonclustered'
                           ELSE NULL
                       END AS flags
                FROM sys.tables AS tbl
                JOIN sys.schemas AS scm ON tbl.schema_id = scm.schema_id
                JOIN sys.indexes AS idx ON tbl.object_id = idx.object_id
                JOIN sys.index_columns AS idxcol ON idx.object_id = idxcol.object_id AND idx.index_id = idxcol.index_id
                JOIN sys.columns AS col ON idxcol.object_id = col.object_id AND idxcol.column_id = col.column_id
                WHERE " . $this->getTableWhereClause($table, 'scm.name', 'tbl.name') . '
                ORDER BY idx.index_id ASC, idxcol.key_ordinal ASC';
    }

    
    public function getListViewsSQL($database)
    {
        return "SELECT name, definition FROM sysobjects
                    INNER JOIN sys.sql_modules ON sysobjects.id = sys.sql_modules.object_id
                WHERE type = 'V' ORDER BY name";
    }

    
    private function getTableWhereClause($table, $schemaColumn, $tableColumn): string
    {
        if (strpos($table, '.') !== false) {
            [$schema, $table] = explode('.', $table);
            $schema           = $this->quoteStringLiteral($schema);
            $table            = $this->quoteStringLiteral($table);
        } else {
            $schema = 'SCHEMA_NAME()';
            $table  = $this->quoteStringLiteral($table);
        }

        return sprintf('(%s = %s AND %s = %s)', $tableColumn, $table, $schemaColumn, $schema);
    }

    
    public function getLocateExpression($str, $substr, $startPos = false)
    {
        if ($startPos === false) {
            return 'CHARINDEX(' . $substr . ', ' . $str . ')';
        }

        return 'CHARINDEX(' . $substr . ', ' . $str . ', ' . $startPos . ')';
    }

    
    public function getModExpression($expression1, $expression2)
    {
        return $expression1 . ' % ' . $expression2;
    }

    
    public function getTrimExpression($str, $mode = TrimMode::UNSPECIFIED, $char = false)
    {
        if ($char === false) {
            switch ($mode) {
                case TrimMode::LEADING:
                    $trimFn = 'LTRIM';
                    break;

                case TrimMode::TRAILING:
                    $trimFn = 'RTRIM';
                    break;

                default:
                    return 'LTRIM(RTRIM(' . $str . '))';
            }

            return $trimFn . '(' . $str . ')';
        }

        $pattern = "'%[^' + " . $char . " + ']%'";

        if ($mode === TrimMode::LEADING) {
            return 'stuff(' . $str . ', 1, patindex(' . $pattern . ', ' . $str . ') - 1, null)';
        }

        if ($mode === TrimMode::TRAILING) {
            return 'reverse(stuff(reverse(' . $str . '), 1, '
                . 'patindex(' . $pattern . ', reverse(' . $str . ')) - 1, null))';
        }

        return 'reverse(stuff(reverse(stuff(' . $str . ', 1, patindex(' . $pattern . ', ' . $str . ') - 1, null)), 1, '
            . 'patindex(' . $pattern . ', reverse(stuff(' . $str . ', 1, patindex(' . $pattern . ', ' . $str
            . ') - 1, null))) - 1, null))';
    }

    
    public function getConcatExpression()
    {
        return sprintf('CONCAT(%s)', implode(', ', func_get_args()));
    }

    
    public function getListDatabasesSQL()
    {
        return 'SELECT * FROM sys.databases';
    }

    
    public function getListNamespacesSQL()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'SQLServerPlatform::getListNamespacesSQL() is deprecated,'
                . ' use SQLServerSchemaManager::listSchemaNames() instead.',
        );

        return "SELECT name FROM sys.schemas WHERE name NOT IN('guest', 'INFORMATION_SCHEMA', 'sys')";
    }

    
    public function getSubstringExpression($string, $start, $length = null)
    {
        if ($length !== null) {
            return 'SUBSTRING(' . $string . ', ' . $start . ', ' . $length . ')';
        }

        return 'SUBSTRING(' . $string . ', ' . $start . ', LEN(' . $string . ') - ' . $start . ' + 1)';
    }

    
    public function getLengthExpression($column)
    {
        return 'LEN(' . $column . ')';
    }

    public function getCurrentDatabaseExpression(): string
    {
        return 'DB_NAME()';
    }

    
    public function getSetTransactionIsolationSQL($level)
    {
        return 'SET TRANSACTION ISOLATION LEVEL ' . $this->_getTransactionIsolationLevelSQL($level);
    }

    
    public function getIntegerTypeDeclarationSQL(array $column)
    {
        return 'INT' . $this->_getCommonIntegerTypeDeclarationSQL($column);
    }

    
    public function getBigIntTypeDeclarationSQL(array $column)
    {
        return 'BIGINT' . $this->_getCommonIntegerTypeDeclarationSQL($column);
    }

    
    public function getSmallIntTypeDeclarationSQL(array $column)
    {
        return 'SMALLINT' . $this->_getCommonIntegerTypeDeclarationSQL($column);
    }

    
    public function getGuidTypeDeclarationSQL(array $column)
    {
        return 'UNIQUEIDENTIFIER';
    }

    
    public function getDateTimeTzTypeDeclarationSQL(array $column)
    {
        return 'DATETIMEOFFSET(6)';
    }

    
    public function getAsciiStringTypeDeclarationSQL(array $column): string
    {
        $length = $column['length'] ?? null;

        if (! isset($column['fixed'])) {
            return sprintf('VARCHAR(%d)', $length ?? 255);
        }

        return sprintf('CHAR(%d)', $length ?? 255);
    }

    
    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed)
    {
        if ($length <= 0 || (func_num_args() > 2 && func_get_arg(2))) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Relying on the default string column length on SQL Server is deprecated'
                    . ', specify the length explicitly.',
            );
        }

        return $fixed
            ? 'NCHAR(' . ($length > 0 ? $length : 255) . ')'
            : 'NVARCHAR(' . ($length > 0 ? $length : 255) . ')';
    }

    
    protected function getBinaryTypeDeclarationSQLSnippet($length, $fixed)
    {
        if ($length <= 0 || (func_num_args() > 2 && func_get_arg(2))) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Relying on the default binary column length on SQL Server is deprecated'
                    . ', specify the length explicitly.',
            );
        }

        return $fixed
            ? 'BINARY(' . ($length > 0 ? $length : 255) . ')'
            : 'VARBINARY(' . ($length > 0 ? $length : 255) . ')';
    }

    
    public function getBinaryMaxLength()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'SQLServerPlatform::getBinaryMaxLength() is deprecated.',
        );

        return 8000;
    }

    
    public function getClobTypeDeclarationSQL(array $column)
    {
        return 'VARCHAR(MAX)';
    }

    
    protected function _getCommonIntegerTypeDeclarationSQL(array $column)
    {
        return ! empty($column['autoincrement']) ? ' IDENTITY' : '';
    }

    
    public function getDateTimeTypeDeclarationSQL(array $column)
    {
        
        
        return 'DATETIME2(6)';
    }

    
    public function getDateTypeDeclarationSQL(array $column)
    {
        return 'DATE';
    }

    
    public function getTimeTypeDeclarationSQL(array $column)
    {
        return 'TIME(0)';
    }

    
    public function getBooleanTypeDeclarationSQL(array $column)
    {
        return 'BIT';
    }

    
    protected function doModifyLimitQuery($query, $limit, $offset)
    {
        if ($limit === null && $offset <= 0) {
            return $query;
        }

        if ($this->shouldAddOrderBy($query)) {
            if (preg_match('/^SELECT\s+DISTINCT/im', $query) > 0) {
                
                
                
                
                
                $query .= ' ORDER BY 1';
            } else {
                
                
                $query .= ' ORDER BY (SELECT 0)';
            }
        }

        
        
        
        $query .= sprintf(' OFFSET %d ROWS', $offset);

        if ($limit !== null) {
            $query .= sprintf(' FETCH NEXT %d ROWS ONLY', $limit);
        }

        return $query;
    }

    
    public function convertBooleans($item)
    {
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                if (! is_bool($value) && ! is_numeric($value)) {
                    continue;
                }

                $item[$key] = (int) (bool) $value;
            }
        } elseif (is_bool($item) || is_numeric($item)) {
            $item = (int) (bool) $item;
        }

        return $item;
    }

    
    public function getCreateTemporaryTableSnippetSQL()
    {
        return 'CREATE TABLE';
    }

    
    public function getTemporaryTableName($tableName)
    {
        return '#' . $tableName;
    }

    
    public function getDateTimeFormatString()
    {
        return 'Y-m-d H:i:s.u';
    }

    
    public function getDateFormatString()
    {
        return 'Y-m-d';
    }

    
    public function getTimeFormatString()
    {
        return 'H:i:s';
    }

    
    public function getDateTimeTzFormatString()
    {
        return 'Y-m-d H:i:s.u P';
    }

    
    public function getName()
    {
        return 'mssql';
    }

    
    protected function initializeDoctrineTypeMappings()
    {
        $this->doctrineTypeMapping = [
            'bigint'           => 'bigint',
            'binary'           => 'binary',
            'bit'              => 'boolean',
            'blob'             => 'blob',
            'char'             => 'string',
            'date'             => 'date',
            'datetime'         => 'datetime',
            'datetime2'        => 'datetime',
            'datetimeoffset'   => 'datetimetz',
            'decimal'          => 'decimal',
            'double'           => 'float',
            'double precision' => 'float',
            'float'            => 'float',
            'image'            => 'blob',
            'int'              => 'integer',
            'money'            => 'integer',
            'nchar'            => 'string',
            'ntext'            => 'text',
            'numeric'          => 'decimal',
            'nvarchar'         => 'string',
            'real'             => 'float',
            'smalldatetime'    => 'datetime',
            'smallint'         => 'smallint',
            'smallmoney'       => 'integer',
            'text'             => 'text',
            'time'             => 'time',
            'tinyint'          => 'smallint',
            'uniqueidentifier' => 'guid',
            'varbinary'        => 'binary',
            'varchar'          => 'string',
        ];
    }

    
    public function createSavePoint($savepoint)
    {
        return 'SAVE TRANSACTION ' . $savepoint;
    }

    
    public function releaseSavePoint($savepoint)
    {
        return '';
    }

    
    public function rollbackSavePoint($savepoint)
    {
        return 'ROLLBACK TRANSACTION ' . $savepoint;
    }

    
    public function getForeignKeyReferentialActionSQL($action)
    {
        
        if (strtoupper($action) === 'RESTRICT') {
            return 'NO ACTION';
        }

        return parent::getForeignKeyReferentialActionSQL($action);
    }

    public function appendLockHint(string $fromClause, int $lockMode): string
    {
        switch ($lockMode) {
            case LockMode::NONE:
            case LockMode::OPTIMISTIC:
                return $fromClause;

            case LockMode::PESSIMISTIC_READ:
                return $fromClause . ' WITH (HOLDLOCK, ROWLOCK)';

            case LockMode::PESSIMISTIC_WRITE:
                return $fromClause . ' WITH (UPDLOCK, ROWLOCK)';

            default:
                throw InvalidLockMode::fromLockMode($lockMode);
        }
    }

    
    public function getForUpdateSQL()
    {
        return ' ';
    }

    
    protected function getReservedKeywordsClass()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'SQLServerPlatform::getReservedKeywordsClass() is deprecated,'
                . ' use SQLServerPlatform::createReservedKeywordsList() instead.',
        );

        return Keywords\SQLServer2012Keywords::class;
    }

    
    public function quoteSingleIdentifier($str)
    {
        return '[' . str_replace(']', ']]', $str) . ']';
    }

    
    public function getTruncateTableSQL($tableName, $cascade = false)
    {
        $tableIdentifier = new Identifier($tableName);

        return 'TRUNCATE TABLE ' . $tableIdentifier->getQuotedName($this);
    }

    
    public function getBlobTypeDeclarationSQL(array $column)
    {
        return 'VARBINARY(MAX)';
    }

    
    public function getColumnDeclarationSQL($name, array $column)
    {
        if (isset($column['columnDefinition'])) {
            $columnDef = $this->getCustomTypeDeclarationSQL($column);
        } else {
            $collation = ! empty($column['collation']) ?
                ' ' . $this->getColumnCollationDeclarationSQL($column['collation']) : '';

            $notnull = ! empty($column['notnull']) ? ' NOT NULL' : '';

            if (! empty($column['unique'])) {
                Deprecation::trigger(
                    'doctrine/dbal',
                    'https:
                    'The usage of the "unique" column property is deprecated. Use unique constraints instead.',
                );

                $unique = ' ' . $this->getUniqueFieldDeclarationSQL();
            } else {
                $unique = '';
            }

            if (! empty($column['check'])) {
                Deprecation::trigger(
                    'doctrine/dbal',
                    'https:
                    'The usage of the "check" column property is deprecated.',
                );

                $check = ' ' . $column['check'];
            } else {
                $check = '';
            }

            $typeDecl  = $column['type']->getSQLDeclaration($column, $this);
            $columnDef = $typeDecl . $collation . $notnull . $unique . $check;
        }

        return $name . ' ' . $columnDef;
    }

    
    public function getColumnCollationDeclarationSQL($collation)
    {
        return 'COLLATE ' . $collation;
    }

    public function columnsEqual(Column $column1, Column $column2): bool
    {
        if (! parent::columnsEqual($column1, $column2)) {
            return false;
        }

        return $this->getDefaultValueDeclarationSQL($column1->toArray())
            === $this->getDefaultValueDeclarationSQL($column2->toArray());
    }

    protected function getLikeWildcardCharacters(): string
    {
        return parent::getLikeWildcardCharacters() . '[]^';
    }

    
    private function generateDefaultConstraintName($table, $column): string
    {
        return 'DF_' . $this->generateIdentifierName($table) . '_' . $this->generateIdentifierName($column);
    }

    
    private function generateIdentifierName($identifier): string
    {
        
        $identifier = new Identifier($identifier);

        return strtoupper(dechex(crc32($identifier->getName())));
    }

    protected function getCommentOnTableSQL(string $tableName, ?string $comment): string
    {
        return sprintf(
            <<<'SQL'
                EXEC sys.sp_addextendedproperty @name=N'MS_Description',
                  @value=N%s, @level0type=N'SCHEMA', @level0name=N'dbo',
                  @level1type=N'TABLE', @level1name=N%s
                SQL
            ,
            $this->quoteStringLiteral((string) $comment),
            $this->quoteStringLiteral($tableName),
        );
    }

    
    public function getListTableMetadataSQL(string $table): string
    {
        return sprintf(
            <<<'SQL'
                SELECT
                  p.value AS [table_comment]
                FROM
                  sys.tables AS tbl
                  INNER JOIN sys.extended_properties AS p ON p.major_id=tbl.object_id AND p.minor_id=0 AND p.class=1
                WHERE
                  (tbl.name=N%s and SCHEMA_NAME(tbl.schema_id)=N'dbo' and p.name=N'MS_Description')
                SQL
            ,
            $this->quoteStringLiteral($table),
        );
    }

    
    private function shouldAddOrderBy($query): bool
    {
        
        
        $matches      = [];
        $matchesCount = preg_match_all('/[\\s]+order\\s+by\\s/im', $query, $matches, PREG_OFFSET_CAPTURE);
        if ($matchesCount === 0) {
            return true;
        }

        
        
        
        
        
        
        while ($matchesCount > 0) {
            $orderByPos          = $matches[0][--$matchesCount][1];
            $openBracketsCount   = substr_count($query, '(', $orderByPos);
            $closedBracketsCount = substr_count($query, ')', $orderByPos);
            if ($openBracketsCount === $closedBracketsCount) {
                return false;
            }
        }

        return true;
    }

    public function createSchemaManager(Connection $connection): SQLServerSchemaManager
    {
        return new SQLServerSchemaManager($connection, $this);
    }
}

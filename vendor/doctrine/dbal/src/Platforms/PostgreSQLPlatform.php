<?php

namespace Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Identifier;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\BinaryType;
use Doctrine\DBAL\Types\BlobType;
use Doctrine\Deprecations\Deprecation;
use UnexpectedValueException;

use function array_diff;
use function array_merge;
use function array_unique;
use function array_values;
use function count;
use function explode;
use function implode;
use function in_array;
use function is_array;
use function is_bool;
use function is_numeric;
use function is_string;
use function sprintf;
use function strpos;
use function strtolower;
use function trim;


class PostgreSQLPlatform extends AbstractPlatform
{
    private bool $useBooleanTrueFalseStrings = true;

    
    private array $booleanLiterals = [
        'true' => [
            't',
            'true',
            'y',
            'yes',
            'on',
            '1',
        ],
        'false' => [
            'f',
            'false',
            'n',
            'no',
            'off',
            '0',
        ],
    ];

    
    public function setUseBooleanTrueFalseStrings($flag)
    {
        $this->useBooleanTrueFalseStrings = (bool) $flag;
    }

    
    public function getSubstringExpression($string, $start, $length = null)
    {
        if ($length === null) {
            return 'SUBSTRING(' . $string . ' FROM ' . $start . ')';
        }

        return 'SUBSTRING(' . $string . ' FROM ' . $start . ' FOR ' . $length . ')';
    }

    
    public function getNowExpression()
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'PostgreSQLPlatform::getNowExpression() is deprecated. Generate dates within the application.',
        );

        return 'LOCALTIMESTAMP(0)';
    }

    
    public function getRegexpExpression()
    {
        return 'SIMILAR TO';
    }

    
    public function getLocateExpression($str, $substr, $startPos = false)
    {
        if ($startPos !== false) {
            $str = $this->getSubstringExpression($str, $startPos);

            return 'CASE WHEN (POSITION(' . $substr . ' IN ' . $str . ') = 0) THEN 0'
                . ' ELSE (POSITION(' . $substr . ' IN ' . $str . ') + ' . $startPos . ' - 1) END';
        }

        return 'POSITION(' . $substr . ' IN ' . $str . ')';
    }

    
    protected function getDateArithmeticIntervalExpression($date, $operator, $interval, $unit)
    {
        if ($unit === DateIntervalUnit::QUARTER) {
            $interval *= 3;
            $unit      = DateIntervalUnit::MONTH;
        }

        return '(' . $date . ' ' . $operator . ' (' . $interval . " || ' " . $unit . "')::interval)";
    }

    
    public function getDateDiffExpression($date1, $date2)
    {
        return '(DATE(' . $date1 . ')-DATE(' . $date2 . '))';
    }

    public function getCurrentDatabaseExpression(): string
    {
        return 'CURRENT_DATABASE()';
    }

    
    public function supportsSequences()
    {
        return true;
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

        return 'public';
    }

    
    public function supportsIdentityColumns()
    {
        return true;
    }

    
    public function supportsPartialIndexes()
    {
        return true;
    }

    
    public function usesSequenceEmulatedIdentityColumns()
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            '%s is deprecated.',
            __METHOD__,
        );

        return true;
    }

    
    public function getIdentitySequenceName($tableName, $columnName)
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            '%s is deprecated.',
            __METHOD__,
        );

        return $tableName . '_' . $columnName . '_seq';
    }

    
    public function supportsCommentOnStatement()
    {
        return true;
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

    
    public function getListDatabasesSQL()
    {
        return 'SELECT datname FROM pg_database';
    }

    
    public function getListNamespacesSQL()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'PostgreSQLPlatform::getListNamespacesSQL() is deprecated,'
                . ' use PostgreSQLSchemaManager::listSchemaNames() instead.',
        );

        return "SELECT schema_name AS nspname
                FROM   information_schema.schemata
                WHERE  schema_name NOT LIKE 'pg\_%'
                AND    schema_name != 'information_schema'";
    }

    
    public function getListSequencesSQL($database)
    {
        return 'SELECT sequence_name AS relname,
                       sequence_schema AS schemaname,
                       minimum_value AS min_value,
                       increment AS increment_by
                FROM   information_schema.sequences
                WHERE  sequence_catalog = ' . $this->quoteStringLiteral($database) . "
                AND    sequence_schema NOT LIKE 'pg\_%'
                AND    sequence_schema != 'information_schema'";
    }

    
    public function getListTablesSQL()
    {
        return "SELECT quote_ident(table_name) AS table_name,
                       table_schema AS schema_name
                FROM   information_schema.tables
                WHERE  table_schema NOT LIKE 'pg\_%'
                AND    table_schema != 'information_schema'
                AND    table_name != 'geometry_columns'
                AND    table_name != 'spatial_ref_sys'
                AND    table_type != 'VIEW'";
    }

    
    public function getListViewsSQL($database)
    {
        return 'SELECT quote_ident(table_name) AS viewname,
                       table_schema AS schemaname,
                       view_definition AS definition
                FROM   information_schema.views
                WHERE  view_definition IS NOT NULL';
    }

    
    public function getListTableForeignKeysSQL($table, $database = null)
    {
        return 'SELECT quote_ident(r.conname) as conname, pg_catalog.pg_get_constraintdef(r.oid, true) as condef
                  FROM pg_catalog.pg_constraint r
                  WHERE r.conrelid =
                  (
                      SELECT c.oid
                      FROM pg_catalog.pg_class c, pg_catalog.pg_namespace n
                      WHERE ' . $this->getTableWhereClause($table) . " AND n.oid = c.relnamespace
                  )
                  AND r.contype = 'f'";
    }

    
    public function getListTableConstraintsSQL($table)
    {
        $table = new Identifier($table);
        $table = $this->quoteStringLiteral($table->getName());

        return sprintf(
            <<<'SQL'
SELECT
    quote_ident(relname) as relname
FROM
    pg_class
WHERE oid IN (
    SELECT indexrelid
    FROM pg_index, pg_class
    WHERE pg_class.relname = %s
        AND pg_class.oid = pg_index.indrelid
        AND (indisunique = 't' OR indisprimary = 't')
    )
SQL
            ,
            $table,
        );
    }

    
    public function getListTableIndexesSQL($table, $database = null)
    {
        return 'SELECT quote_ident(relname) as relname, pg_index.indisunique, pg_index.indisprimary,
                       pg_index.indkey, pg_index.indrelid,
                       pg_get_expr(indpred, indrelid) AS where
                 FROM pg_class, pg_index
                 WHERE oid IN (
                    SELECT indexrelid
                    FROM pg_index si, pg_class sc, pg_namespace sn
                    WHERE ' . $this->getTableWhereClause($table, 'sc', 'sn') . '
                    AND sc.oid=si.indrelid AND sc.relnamespace = sn.oid
                 ) AND pg_index.indexrelid = oid';
    }

    
    private function getTableWhereClause($table, $classAlias = 'c', $namespaceAlias = 'n'): string
    {
        $whereClause = $namespaceAlias . ".nspname NOT IN ('pg_catalog', 'information_schema', 'pg_toast') AND ";
        if (strpos($table, '.') !== false) {
            [$schema, $table] = explode('.', $table);
            $schema           = $this->quoteStringLiteral($schema);
        } else {
            $schema = 'ANY(current_schemas(false))';
        }

        $table = new Identifier($table);
        $table = $this->quoteStringLiteral($table->getName());

        return $whereClause . sprintf(
            '%s.relname = %s AND %s.nspname = %s',
            $classAlias,
            $table,
            $namespaceAlias,
            $schema,
        );
    }

    
    public function getListTableColumnsSQL($table, $database = null)
    {
        return "SELECT
                    a.attnum,
                    quote_ident(a.attname) AS field,
                    t.typname AS type,
                    format_type(a.atttypid, a.atttypmod) AS complete_type,
                    (SELECT tc.collcollate FROM pg_catalog.pg_collation tc WHERE tc.oid = a.attcollation) AS collation,
                    (SELECT t1.typname FROM pg_catalog.pg_type t1 WHERE t1.oid = t.typbasetype) AS domain_type,
                    (SELECT format_type(t2.typbasetype, t2.typtypmod) FROM
                      pg_catalog.pg_type t2 WHERE t2.typtype = 'd' AND t2.oid = a.atttypid) AS domain_complete_type,
                    a.attnotnull AS isnotnull,
                    (SELECT 't'
                     FROM pg_index
                     WHERE c.oid = pg_index.indrelid
                        AND pg_index.indkey[0] = a.attnum
                        AND pg_index.indisprimary = 't'
                    ) AS pri,
                    (SELECT pg_get_expr(adbin, adrelid)
                     FROM pg_attrdef
                     WHERE c.oid = pg_attrdef.adrelid
                        AND pg_attrdef.adnum=a.attnum
                    ) AS default,
                    (SELECT pg_description.description
                        FROM pg_description WHERE pg_description.objoid = c.oid AND a.attnum = pg_description.objsubid
                    ) AS comment
                    FROM pg_attribute a, pg_class c, pg_type t, pg_namespace n
                    WHERE " . $this->getTableWhereClause($table, 'c', 'n') . '
                        AND a.attnum > 0
                        AND a.attrelid = c.oid
                        AND a.atttypid = t.oid
                        AND n.oid = c.relnamespace
                    ORDER BY a.attnum';
    }

    
    public function getAdvancedForeignKeyOptionsSQL(ForeignKeyConstraint $foreignKey)
    {
        $query = '';

        if ($foreignKey->hasOption('match')) {
            $query .= ' MATCH ' . $foreignKey->getOption('match');
        }

        $query .= parent::getAdvancedForeignKeyOptionsSQL($foreignKey);

        if ($foreignKey->hasOption('deferrable') && $foreignKey->getOption('deferrable') !== false) {
            $query .= ' DEFERRABLE';
        } else {
            $query .= ' NOT DEFERRABLE';
        }

        if (
            ($foreignKey->hasOption('feferred') && $foreignKey->getOption('feferred') !== false)
            || ($foreignKey->hasOption('deferred') && $foreignKey->getOption('deferred') !== false)
        ) {
            $query .= ' INITIALLY DEFERRED';
        } else {
            $query .= ' INITIALLY IMMEDIATE';
        }

        return $query;
    }

    
    public function getAlterTableSQL(TableDiff $diff)
    {
        $sql         = [];
        $commentsSQL = [];
        $columnSql   = [];

        $table = $diff->getOldTable() ?? $diff->getName($this);

        $tableNameSQL = $table->getQuotedName($this);

        foreach ($diff->getAddedColumns() as $addedColumn) {
            if ($this->onSchemaAlterTableAddColumn($addedColumn, $diff, $columnSql)) {
                continue;
            }

            $query = 'ADD ' . $this->getColumnDeclarationSQL(
                $addedColumn->getQuotedName($this),
                $addedColumn->toArray(),
            );

            $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' ' . $query;

            $comment = $this->getColumnComment($addedColumn);

            if ($comment === null || $comment === '') {
                continue;
            }

            $commentsSQL[] = $this->getCommentOnColumnSQL(
                $tableNameSQL,
                $addedColumn->getQuotedName($this),
                $comment,
            );
        }

        foreach ($diff->getDroppedColumns() as $droppedColumn) {
            if ($this->onSchemaAlterTableRemoveColumn($droppedColumn, $diff, $columnSql)) {
                continue;
            }

            $query = 'DROP ' . $droppedColumn->getQuotedName($this);
            $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' ' . $query;
        }

        foreach ($diff->getModifiedColumns() as $columnDiff) {
            if ($this->onSchemaAlterTableChangeColumn($columnDiff, $diff, $columnSql)) {
                continue;
            }

            if ($this->isUnchangedBinaryColumn($columnDiff)) {
                continue;
            }

            $oldColumn = $columnDiff->getOldColumn() ?? $columnDiff->getOldColumnName();
            $newColumn = $columnDiff->getNewColumn();

            $oldColumnName = $oldColumn->getQuotedName($this);

            if (
                $columnDiff->hasTypeChanged()
                || $columnDiff->hasPrecisionChanged()
                || $columnDiff->hasScaleChanged()
                || $columnDiff->hasFixedChanged()
            ) {
                $type = $newColumn->getType();

                
                $columnDefinition                  = $newColumn->toArray();
                $columnDefinition['autoincrement'] = false;

                
                $query = 'ALTER ' . $oldColumnName . ' TYPE ' . $type->getSQLDeclaration($columnDefinition, $this);
                $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' ' . $query;
            }

            if ($columnDiff->hasDefaultChanged()) {
                $defaultClause = $newColumn->getDefault() === null
                    ? ' DROP DEFAULT'
                    : ' SET' . $this->getDefaultValueDeclarationSQL($newColumn->toArray());

                $query = 'ALTER ' . $oldColumnName . $defaultClause;
                $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' ' . $query;
            }

            if ($columnDiff->hasNotNullChanged()) {
                $query = 'ALTER ' . $oldColumnName . ' ' . ($newColumn->getNotnull() ? 'SET' : 'DROP') . ' NOT NULL';
                $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' ' . $query;
            }

            if ($columnDiff->hasAutoIncrementChanged()) {
                if ($newColumn->getAutoincrement()) {
                    
                    $seqName = $this->getIdentitySequenceName(
                        $table->getName(),
                        $oldColumnName,
                    );

                    $sql[] = 'CREATE SEQUENCE ' . $seqName;
                    $sql[] = "SELECT setval('" . $seqName . "', (SELECT MAX(" . $oldColumnName . ') FROM '
                        . $tableNameSQL . '))';
                    $query = 'ALTER ' . $oldColumnName . " SET DEFAULT nextval('" . $seqName . "')";
                } else {
                    
                    $query = 'ALTER ' . $oldColumnName . ' DROP DEFAULT';
                }

                $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' ' . $query;
            }

            $oldComment = $this->getOldColumnComment($columnDiff);
            $newComment = $this->getColumnComment($newColumn);

            if (
                $columnDiff->hasCommentChanged()
                || ($columnDiff->getOldColumn() !== null && $oldComment !== $newComment)
            ) {
                $commentsSQL[] = $this->getCommentOnColumnSQL(
                    $tableNameSQL,
                    $newColumn->getQuotedName($this),
                    $newComment,
                );
            }

            if (! $columnDiff->hasLengthChanged()) {
                continue;
            }

            $query = 'ALTER ' . $oldColumnName . ' TYPE '
                . $newColumn->getType()->getSQLDeclaration($newColumn->toArray(), $this);
            $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' ' . $query;
        }

        foreach ($diff->getRenamedColumns() as $oldColumnName => $column) {
            if ($this->onSchemaAlterTableRenameColumn($oldColumnName, $column, $diff, $columnSql)) {
                continue;
            }

            $oldColumnName = new Identifier($oldColumnName);

            $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' RENAME COLUMN ' . $oldColumnName->getQuotedName($this)
                . ' TO ' . $column->getQuotedName($this);
        }

        $tableSql = [];

        if (! $this->onSchemaAlterTable($diff, $tableSql)) {
            $sql = array_merge($sql, $commentsSQL);

            $newName = $diff->getNewName();

            if ($newName !== false) {
                Deprecation::trigger(
                    'doctrine/dbal',
                    'https:
                    'Generation of "rename table" SQL using %s is deprecated. Use getRenameTableSQL() instead.',
                    __METHOD__,
                );

                $sql[] = sprintf(
                    'ALTER TABLE %s RENAME TO %s',
                    $tableNameSQL,
                    $newName->getQuotedName($this),
                );
            }

            $sql = array_merge(
                $this->getPreAlterTableIndexForeignKeySQL($diff),
                $sql,
                $this->getPostAlterTableIndexForeignKeySQL($diff),
            );
        }

        return array_merge($sql, $tableSql, $columnSql);
    }

    
    private function isUnchangedBinaryColumn(ColumnDiff $columnDiff): bool
    {
        $newColumnType = $columnDiff->getNewColumn()->getType();

        if (! $newColumnType instanceof BinaryType && ! $newColumnType instanceof BlobType) {
            return false;
        }

        $oldColumn = $columnDiff->getOldColumn() instanceof Column ? $columnDiff->getOldColumn() : null;

        if ($oldColumn !== null) {
            $oldColumnType = $oldColumn->getType();

            if (! $oldColumnType instanceof BinaryType && ! $oldColumnType instanceof BlobType) {
                return false;
            }

            return count(array_diff($columnDiff->changedProperties, ['type', 'length', 'fixed'])) === 0;
        }

        if ($columnDiff->hasTypeChanged()) {
            return false;
        }

        return count(array_diff($columnDiff->changedProperties, ['length', 'fixed'])) === 0;
    }

    
    protected function getRenameIndexSQL($oldIndexName, Index $index, $tableName)
    {
        if (strpos($tableName, '.') !== false) {
            [$schema]     = explode('.', $tableName);
            $oldIndexName = $schema . '.' . $oldIndexName;
        }

        return ['ALTER INDEX ' . $oldIndexName . ' RENAME TO ' . $index->getQuotedName($this)];
    }

    
    public function getCommentOnColumnSQL($tableName, $columnName, $comment)
    {
        $tableName  = new Identifier($tableName);
        $columnName = new Identifier($columnName);
        $comment    = $comment === null ? 'NULL' : $this->quoteStringLiteral($comment);

        return sprintf(
            'COMMENT ON COLUMN %s.%s IS %s',
            $tableName->getQuotedName($this),
            $columnName->getQuotedName($this),
            $comment,
        );
    }

    
    public function getCreateSequenceSQL(Sequence $sequence)
    {
        return 'CREATE SEQUENCE ' . $sequence->getQuotedName($this) .
            ' INCREMENT BY ' . $sequence->getAllocationSize() .
            ' MINVALUE ' . $sequence->getInitialValue() .
            ' START ' . $sequence->getInitialValue() .
            $this->getSequenceCacheSQL($sequence);
    }

    
    public function getAlterSequenceSQL(Sequence $sequence)
    {
        return 'ALTER SEQUENCE ' . $sequence->getQuotedName($this) .
            ' INCREMENT BY ' . $sequence->getAllocationSize() .
            $this->getSequenceCacheSQL($sequence);
    }

    
    private function getSequenceCacheSQL(Sequence $sequence): string
    {
        if ($sequence->getCache() > 1) {
            return ' CACHE ' . $sequence->getCache();
        }

        return '';
    }

    
    public function getDropSequenceSQL($sequence)
    {
        return parent::getDropSequenceSQL($sequence) . ' CASCADE';
    }

    
    public function getDropForeignKeySQL($foreignKey, $table)
    {
        return $this->getDropConstraintSQL($foreignKey, $table);
    }

    
    protected function _getCreateTableSQL($name, array $columns, array $options = [])
    {
        $queryFields = $this->getColumnDeclarationListSQL($columns);

        if (isset($options['primary']) && ! empty($options['primary'])) {
            $keyColumns   = array_unique(array_values($options['primary']));
            $queryFields .= ', PRIMARY KEY(' . implode(', ', $keyColumns) . ')';
        }

        $query = 'CREATE TABLE ' . $name . ' (' . $queryFields . ')';

        $sql = [$query];

        if (isset($options['indexes']) && ! empty($options['indexes'])) {
            foreach ($options['indexes'] as $index) {
                $sql[] = $this->getCreateIndexSQL($index, $name);
            }
        }

        if (isset($options['uniqueConstraints'])) {
            foreach ($options['uniqueConstraints'] as $uniqueConstraint) {
                $sql[] = $this->getCreateConstraintSQL($uniqueConstraint, $name);
            }
        }

        if (isset($options['foreignKeys'])) {
            foreach ($options['foreignKeys'] as $definition) {
                $sql[] = $this->getCreateForeignKeySQL($definition, $name);
            }
        }

        return $sql;
    }

    
    private function convertSingleBooleanValue($value, $callback)
    {
        if ($value === null) {
            return $callback(null);
        }

        if (is_bool($value) || is_numeric($value)) {
            return $callback((bool) $value);
        }

        if (! is_string($value)) {
            return $callback(true);
        }

        
        if (in_array(strtolower(trim($value)), $this->booleanLiterals['false'], true)) {
            return $callback(false);
        }

        if (in_array(strtolower(trim($value)), $this->booleanLiterals['true'], true)) {
            return $callback(true);
        }

        throw new UnexpectedValueException(sprintf("Unrecognized boolean literal '%s'", $value));
    }

    
    private function doConvertBooleans($item, $callback)
    {
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                $item[$key] = $this->convertSingleBooleanValue($value, $callback);
            }

            return $item;
        }

        return $this->convertSingleBooleanValue($item, $callback);
    }

    
    public function convertBooleans($item)
    {
        if (! $this->useBooleanTrueFalseStrings) {
            return parent::convertBooleans($item);
        }

        return $this->doConvertBooleans(
            $item,
            
            static function ($value) {
                if ($value === null) {
                    return 'NULL';
                }

                return $value === true ? 'true' : 'false';
            },
        );
    }

    
    public function convertBooleansToDatabaseValue($item)
    {
        if (! $this->useBooleanTrueFalseStrings) {
            return parent::convertBooleansToDatabaseValue($item);
        }

        return $this->doConvertBooleans(
            $item,
            
            static function ($value): ?int {
                return $value === null ? null : (int) $value;
            },
        );
    }

    
    public function convertFromBoolean($item)
    {
        if ($item !== null && in_array(strtolower($item), $this->booleanLiterals['false'], true)) {
            return false;
        }

        return parent::convertFromBoolean($item);
    }

    
    public function getSequenceNextValSQL($sequence)
    {
        return "SELECT NEXTVAL('" . $sequence . "')";
    }

    
    public function getSetTransactionIsolationSQL($level)
    {
        return 'SET SESSION CHARACTERISTICS AS TRANSACTION ISOLATION LEVEL '
            . $this->_getTransactionIsolationLevelSQL($level);
    }

    
    public function getBooleanTypeDeclarationSQL(array $column)
    {
        return 'BOOLEAN';
    }

    
    public function getIntegerTypeDeclarationSQL(array $column)
    {
        if (! empty($column['autoincrement'])) {
            return 'SERIAL';
        }

        return 'INT';
    }

    
    public function getBigIntTypeDeclarationSQL(array $column)
    {
        if (! empty($column['autoincrement'])) {
            return 'BIGSERIAL';
        }

        return 'BIGINT';
    }

    
    public function getSmallIntTypeDeclarationSQL(array $column)
    {
        if (! empty($column['autoincrement'])) {
            return 'SMALLSERIAL';
        }

        return 'SMALLINT';
    }

    
    public function getGuidTypeDeclarationSQL(array $column)
    {
        return 'UUID';
    }

    
    public function getDateTimeTypeDeclarationSQL(array $column)
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }

    
    public function getDateTimeTzTypeDeclarationSQL(array $column)
    {
        return 'TIMESTAMP(0) WITH TIME ZONE';
    }

    
    public function getDateTypeDeclarationSQL(array $column)
    {
        return 'DATE';
    }

    
    public function getTimeTypeDeclarationSQL(array $column)
    {
        return 'TIME(0) WITHOUT TIME ZONE';
    }

    
    protected function _getCommonIntegerTypeDeclarationSQL(array $column)
    {
        return '';
    }

    
    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed)
    {
        return $fixed ? ($length > 0 ? 'CHAR(' . $length . ')' : 'CHAR(255)')
            : ($length > 0 ? 'VARCHAR(' . $length . ')' : 'VARCHAR(255)');
    }

    
    protected function getBinaryTypeDeclarationSQLSnippet($length, $fixed)
    {
        return 'BYTEA';
    }

    
    public function getClobTypeDeclarationSQL(array $column)
    {
        return 'TEXT';
    }

    
    public function getName()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'PostgreSQLPlatform::getName() is deprecated. Identify platforms by their class.',
        );

        return 'postgresql';
    }

    
    public function getDateTimeTzFormatString()
    {
        return 'Y-m-d H:i:sO';
    }

    
    public function getEmptyIdentityInsertSQL($quotedTableName, $quotedIdentifierColumnName)
    {
        return 'INSERT INTO ' . $quotedTableName . ' (' . $quotedIdentifierColumnName . ') VALUES (DEFAULT)';
    }

    
    public function getTruncateTableSQL($tableName, $cascade = false)
    {
        $tableIdentifier = new Identifier($tableName);
        $sql             = 'TRUNCATE ' . $tableIdentifier->getQuotedName($this);

        if ($cascade) {
            $sql .= ' CASCADE';
        }

        return $sql;
    }

    
    public function getReadLockSQL()
    {
        return 'FOR SHARE';
    }

    
    protected function initializeDoctrineTypeMappings()
    {
        $this->doctrineTypeMapping = [
            'bigint'           => 'bigint',
            'bigserial'        => 'bigint',
            'bool'             => 'boolean',
            'boolean'          => 'boolean',
            'bpchar'           => 'string',
            'bytea'            => 'blob',
            'char'             => 'string',
            'date'             => 'date',
            'datetime'         => 'datetime',
            'decimal'          => 'decimal',
            'double'           => 'float',
            'double precision' => 'float',
            'float'            => 'float',
            'float4'           => 'float',
            'float8'           => 'float',
            'inet'             => 'string',
            'int'              => 'integer',
            'int2'             => 'smallint',
            'int4'             => 'integer',
            'int8'             => 'bigint',
            'integer'          => 'integer',
            'interval'         => 'string',
            'json'             => 'json',
            'jsonb'            => 'json',
            'money'            => 'decimal',
            'numeric'          => 'decimal',
            'serial'           => 'integer',
            'serial4'          => 'integer',
            'serial8'          => 'bigint',
            'real'             => 'float',
            'smallint'         => 'smallint',
            'text'             => 'text',
            'time'             => 'time',
            'timestamp'        => 'datetime',
            'timestamptz'      => 'datetimetz',
            'timetz'           => 'time',
            'tsvector'         => 'text',
            'uuid'             => 'guid',
            'varchar'          => 'string',
            'year'             => 'date',
            '_varchar'         => 'string',
        ];
    }

    
    public function getVarcharMaxLength()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'PostgreSQLPlatform::getVarcharMaxLength() is deprecated.',
        );

        return 65535;
    }

    
    public function getBinaryMaxLength()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'PostgreSQLPlatform::getBinaryMaxLength() is deprecated.',
        );

        return 0;
    }

    
    public function getBinaryDefaultLength()
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'Relying on the default binary column length is deprecated, specify the length explicitly.',
        );

        return 0;
    }

    
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

    
    protected function getReservedKeywordsClass()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'PostgreSQLPlatform::getReservedKeywordsClass() is deprecated,'
                . ' use PostgreSQLPlatform::createReservedKeywordsList() instead.',
        );

        return Keywords\PostgreSQL94Keywords::class;
    }

    
    public function getBlobTypeDeclarationSQL(array $column)
    {
        return 'BYTEA';
    }

    
    public function getDefaultValueDeclarationSQL($column)
    {
        if (isset($column['autoincrement']) && $column['autoincrement'] === true) {
            return '';
        }

        return parent::getDefaultValueDeclarationSQL($column);
    }

    
    public function supportsColumnCollation()
    {
        return true;
    }

    
    public function getJsonTypeDeclarationSQL(array $column)
    {
        if (! empty($column['jsonb'])) {
            return 'JSONB';
        }

        return 'JSON';
    }

    private function getOldColumnComment(ColumnDiff $columnDiff): ?string
    {
        $oldColumn = $columnDiff->getOldColumn();

        if ($oldColumn !== null) {
            return $this->getColumnComment($oldColumn);
        }

        return null;
    }

    
    public function getListTableMetadataSQL(string $table, ?string $schema = null): string
    {
        if ($schema !== null) {
            $table = $schema . '.' . $table;
        }

        return sprintf(
            <<<'SQL'
SELECT obj_description(%s::regclass) AS table_comment;
SQL
            ,
            $this->quoteStringLiteral($table),
        );
    }

    public function createSchemaManager(Connection $connection): PostgreSQLSchemaManager
    {
        return new PostgreSQLSchemaManager($connection, $this);
    }
}

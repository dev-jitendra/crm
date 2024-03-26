<?php

namespace Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\DB2SchemaManager;
use Doctrine\DBAL\Schema\Identifier;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\Deprecations\Deprecation;

use function array_merge;
use function count;
use function current;
use function explode;
use function func_get_arg;
use function func_num_args;
use function implode;
use function sprintf;
use function strpos;

class DB2Platform extends AbstractPlatform
{
    
    public function getCharMaxLength(): int
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'DB2Platform::getCharMaxLength() is deprecated.',
        );

        return 254;
    }

    
    public function getBinaryMaxLength()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'DB2Platform::getBinaryMaxLength() is deprecated.',
        );

        return 32704;
    }

    
    public function getBinaryDefaultLength()
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'Relying on the default binary column length is deprecated, specify the length explicitly.',
        );

        return 1;
    }

    
    public function getVarcharTypeDeclarationSQL(array $column)
    {
        
        if (! isset($column['length']) && ! empty($column['fixed'])) {
            $column['length'] = $this->getCharMaxLength();
        }

        return parent::getVarcharTypeDeclarationSQL($column);
    }

    
    public function getBlobTypeDeclarationSQL(array $column)
    {
        
        return 'BLOB(1M)';
    }

    
    protected function initializeDoctrineTypeMappings()
    {
        $this->doctrineTypeMapping = [
            'bigint'    => 'bigint',
            'binary'    => 'binary',
            'blob'      => 'blob',
            'character' => 'string',
            'clob'      => 'text',
            'date'      => 'date',
            'decimal'   => 'decimal',
            'double'    => 'float',
            'integer'   => 'integer',
            'real'      => 'float',
            'smallint'  => 'smallint',
            'time'      => 'time',
            'timestamp' => 'datetime',
            'varbinary' => 'binary',
            'varchar'   => 'string',
        ];
    }

    
    public function isCommentedDoctrineType(Type $doctrineType)
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            '%s is deprecated and will be removed in Doctrine DBAL 4.0. Use Type::requiresSQLCommentHint() instead.',
            __METHOD__,
        );

        if ($doctrineType->getName() === Types::BOOLEAN) {
            
            
            return true;
        }

        return parent::isCommentedDoctrineType($doctrineType);
    }

    
    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed)
    {
        if ($length <= 0 || (func_num_args() > 2 && func_get_arg(2))) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Relying on the default string column length on IBM DB2 is deprecated'
                    . ', specify the length explicitly.',
            );
        }

        return $fixed ? ($length > 0 ? 'CHAR(' . $length . ')' : 'CHAR(254)')
                : ($length > 0 ? 'VARCHAR(' . $length . ')' : 'VARCHAR(255)');
    }

    
    protected function getBinaryTypeDeclarationSQLSnippet($length, $fixed)
    {
        if ($length <= 0 || (func_num_args() > 2 && func_get_arg(2))) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Relying on the default binary column length on IBM DB2 is deprecated'
                . ', specify the length explicitly.',
            );
        }

        return $this->getVarcharTypeDeclarationSQLSnippet($length, $fixed) . ' FOR BIT DATA';
    }

    
    public function getClobTypeDeclarationSQL(array $column)
    {
        
        return 'CLOB(1M)';
    }

    
    public function getName()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'DB2Platform::getName() is deprecated. Identify platforms by their class.',
        );

        return 'db2';
    }

    
    public function getBooleanTypeDeclarationSQL(array $column)
    {
        return 'SMALLINT';
    }

    
    public function getIntegerTypeDeclarationSQL(array $column)
    {
        return 'INTEGER' . $this->_getCommonIntegerTypeDeclarationSQL($column);
    }

    
    public function getBigIntTypeDeclarationSQL(array $column)
    {
        return 'BIGINT' . $this->_getCommonIntegerTypeDeclarationSQL($column);
    }

    
    public function getSmallIntTypeDeclarationSQL(array $column)
    {
        return 'SMALLINT' . $this->_getCommonIntegerTypeDeclarationSQL($column);
    }

    
    protected function _getCommonIntegerTypeDeclarationSQL(array $column)
    {
        $autoinc = '';
        if (! empty($column['autoincrement'])) {
            $autoinc = ' GENERATED BY DEFAULT AS IDENTITY';
        }

        return $autoinc;
    }

    
    public function getBitAndComparisonExpression($value1, $value2)
    {
        return 'BITAND(' . $value1 . ', ' . $value2 . ')';
    }

    
    public function getBitOrComparisonExpression($value1, $value2)
    {
        return 'BITOR(' . $value1 . ', ' . $value2 . ')';
    }

    
    protected function getDateArithmeticIntervalExpression($date, $operator, $interval, $unit)
    {
        switch ($unit) {
            case DateIntervalUnit::WEEK:
                $interval *= 7;
                $unit      = DateIntervalUnit::DAY;
                break;

            case DateIntervalUnit::QUARTER:
                $interval *= 3;
                $unit      = DateIntervalUnit::MONTH;
                break;
        }

        return $date . ' ' . $operator . ' ' . $interval . ' ' . $unit;
    }

    
    public function getDateDiffExpression($date1, $date2)
    {
        return 'DAYS(' . $date1 . ') - DAYS(' . $date2 . ')';
    }

    
    public function getDateTimeTypeDeclarationSQL(array $column)
    {
        if (isset($column['version']) && $column['version'] === true) {
            return 'TIMESTAMP(0) WITH DEFAULT';
        }

        return 'TIMESTAMP(0)';
    }

    
    public function getDateTypeDeclarationSQL(array $column)
    {
        return 'DATE';
    }

    
    public function getTimeTypeDeclarationSQL(array $column)
    {
        return 'TIME';
    }

    
    public function getTruncateTableSQL($tableName, $cascade = false)
    {
        $tableIdentifier = new Identifier($tableName);

        return 'TRUNCATE ' . $tableIdentifier->getQuotedName($this) . ' IMMEDIATE';
    }

    
    public function getListTableColumnsSQL($table, $database = null)
    {
        $table = $this->quoteStringLiteral($table);

        
        
        
        return "
        SELECT
          cols.default,
          subq.*
        FROM (
               SELECT DISTINCT
                 c.tabschema,
                 c.tabname,
                 c.colname,
                 c.colno,
                 c.typename,
                 c.codepage,
                 c.nulls,
                 c.length,
                 c.scale,
                 c.identity,
                 tc.type AS tabconsttype,
                 c.remarks AS comment,
                 k.colseq,
                 CASE
                 WHEN c.generated = 'D' THEN 1
                 ELSE 0
                 END     AS autoincrement
               FROM syscat.columns c
                 LEFT JOIN (syscat.keycoluse k JOIN syscat.tabconst tc
                     ON (k.tabschema = tc.tabschema
                         AND k.tabname = tc.tabname
                         AND tc.type = 'P'))
                   ON (c.tabschema = k.tabschema
                       AND c.tabname = k.tabname
                       AND c.colname = k.colname)
               WHERE UPPER(c.tabname) = UPPER(" . $table . ')
               ORDER BY c.colno
             ) subq
          JOIN syscat.columns cols
            ON subq.tabschema = cols.tabschema
               AND subq.tabname = cols.tabname
               AND subq.colno = cols.colno
        ORDER BY subq.colno
        ';
    }

    
    public function getListTablesSQL()
    {
        return "SELECT NAME FROM SYSIBM.SYSTABLES WHERE TYPE = 'T' AND CREATOR = CURRENT_USER";
    }

    
    public function getListViewsSQL($database)
    {
        return 'SELECT NAME, TEXT FROM SYSIBM.SYSVIEWS';
    }

    
    public function getListTableIndexesSQL($table, $database = null)
    {
        $table = $this->quoteStringLiteral($table);

        return "SELECT   idx.INDNAME AS key_name,
                         idxcol.COLNAME AS column_name,
                         CASE
                             WHEN idx.UNIQUERULE = 'P' THEN 1
                             ELSE 0
                         END AS primary,
                         CASE
                             WHEN idx.UNIQUERULE = 'D' THEN 1
                             ELSE 0
                         END AS non_unique
                FROM     SYSCAT.INDEXES AS idx
                JOIN     SYSCAT.INDEXCOLUSE AS idxcol
                ON       idx.INDSCHEMA = idxcol.INDSCHEMA AND idx.INDNAME = idxcol.INDNAME
                WHERE    idx.TABNAME = UPPER(" . $table . ')
                ORDER BY idxcol.COLSEQ ASC';
    }

    
    public function getListTableForeignKeysSQL($table)
    {
        $table = $this->quoteStringLiteral($table);

        return "SELECT   fkcol.COLNAME AS local_column,
                         fk.REFTABNAME AS foreign_table,
                         pkcol.COLNAME AS foreign_column,
                         fk.CONSTNAME AS index_name,
                         CASE
                             WHEN fk.UPDATERULE = 'R' THEN 'RESTRICT'
                             ELSE NULL
                         END AS on_update,
                         CASE
                             WHEN fk.DELETERULE = 'C' THEN 'CASCADE'
                             WHEN fk.DELETERULE = 'N' THEN 'SET NULL'
                             WHEN fk.DELETERULE = 'R' THEN 'RESTRICT'
                             ELSE NULL
                         END AS on_delete
                FROM     SYSCAT.REFERENCES AS fk
                JOIN     SYSCAT.KEYCOLUSE AS fkcol
                ON       fk.CONSTNAME = fkcol.CONSTNAME
                AND      fk.TABSCHEMA = fkcol.TABSCHEMA
                AND      fk.TABNAME = fkcol.TABNAME
                JOIN     SYSCAT.KEYCOLUSE AS pkcol
                ON       fk.REFKEYNAME = pkcol.CONSTNAME
                AND      fk.REFTABSCHEMA = pkcol.TABSCHEMA
                AND      fk.REFTABNAME = pkcol.TABNAME
                WHERE    fk.TABNAME = UPPER(" . $table . ')
                ORDER BY fkcol.COLSEQ ASC';
    }

    
    public function supportsCreateDropDatabase()
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            '%s is deprecated.',
            __METHOD__,
        );

        return false;
    }

    
    public function supportsCommentOnStatement()
    {
        return true;
    }

    
    public function getCurrentDateSQL()
    {
        return 'CURRENT DATE';
    }

    
    public function getCurrentTimeSQL()
    {
        return 'CURRENT TIME';
    }

    
    public function getCurrentTimestampSQL()
    {
        return 'CURRENT TIMESTAMP';
    }

    
    public function getIndexDeclarationSQL($name, Index $index)
    {
        
        throw Exception::notSupported(__METHOD__);
    }

    
    protected function _getCreateTableSQL($name, array $columns, array $options = [])
    {
        $indexes = [];
        if (isset($options['indexes'])) {
            $indexes = $options['indexes'];
        }

        $options['indexes'] = [];

        $sqls = parent::_getCreateTableSQL($name, $columns, $options);

        foreach ($indexes as $definition) {
            $sqls[] = $this->getCreateIndexSQL($definition, $name);
        }

        return $sqls;
    }

    
    public function getAlterTableSQL(TableDiff $diff)
    {
        $sql         = [];
        $columnSql   = [];
        $commentsSQL = [];

        $tableNameSQL = ($diff->getOldTable() ?? $diff->getName($this))->getQuotedName($this);

        $queryParts = [];
        foreach ($diff->getAddedColumns() as $column) {
            if ($this->onSchemaAlterTableAddColumn($column, $diff, $columnSql)) {
                continue;
            }

            $columnDef = $column->toArray();
            $queryPart = 'ADD COLUMN ' . $this->getColumnDeclarationSQL($column->getQuotedName($this), $columnDef);

            
            if (
                ! empty($columnDef['notnull']) &&
                ! isset($columnDef['default']) &&
                empty($columnDef['autoincrement'])
            ) {
                $queryPart .= ' WITH DEFAULT';
            }

            $queryParts[] = $queryPart;

            $comment = $this->getColumnComment($column);

            if ($comment === null || $comment === '') {
                continue;
            }

            $commentsSQL[] = $this->getCommentOnColumnSQL(
                $tableNameSQL,
                $column->getQuotedName($this),
                $comment,
            );
        }

        foreach ($diff->getDroppedColumns() as $column) {
            if ($this->onSchemaAlterTableRemoveColumn($column, $diff, $columnSql)) {
                continue;
            }

            $queryParts[] =  'DROP COLUMN ' . $column->getQuotedName($this);
        }

        foreach ($diff->getModifiedColumns() as $columnDiff) {
            if ($this->onSchemaAlterTableChangeColumn($columnDiff, $diff, $columnSql)) {
                continue;
            }

            if ($columnDiff->hasCommentChanged()) {
                $commentsSQL[] = $this->getCommentOnColumnSQL(
                    $tableNameSQL,
                    $columnDiff->getNewColumn()->getQuotedName($this),
                    $this->getColumnComment($columnDiff->getNewColumn()),
                );
            }

            $this->gatherAlterColumnSQL(
                $tableNameSQL,
                $columnDiff,
                $sql,
                $queryParts,
            );
        }

        foreach ($diff->getRenamedColumns() as $oldColumnName => $column) {
            if ($this->onSchemaAlterTableRenameColumn($oldColumnName, $column, $diff, $columnSql)) {
                continue;
            }

            $oldColumnName = new Identifier($oldColumnName);

            $queryParts[] =  'RENAME COLUMN ' . $oldColumnName->getQuotedName($this) .
                ' TO ' . $column->getQuotedName($this);
        }

        $tableSql = [];

        if (! $this->onSchemaAlterTable($diff, $tableSql)) {
            if (count($queryParts) > 0) {
                $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' ' . implode(' ', $queryParts);
            }

            
            if (count($diff->getDroppedColumns()) > 0 || count($diff->getModifiedColumns()) > 0) {
                $sql[] = "CALL SYSPROC.ADMIN_CMD ('REORG TABLE " . $tableNameSQL . "')";
            }

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
                    'RENAME TABLE %s TO %s',
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

    
    public function getRenameTableSQL(string $oldName, string $newName): array
    {
        return [
            sprintf('RENAME TABLE %s TO %s', $oldName, $newName),
        ];
    }

    
    private function gatherAlterColumnSQL(
        string $table,
        ColumnDiff $columnDiff,
        array &$sql,
        array &$queryParts
    ): void {
        $alterColumnClauses = $this->getAlterColumnClausesSQL($columnDiff);

        if (empty($alterColumnClauses)) {
            return;
        }

        
        if (count($alterColumnClauses) === 1) {
            $queryParts[] = current($alterColumnClauses);

            return;
        }

        
        
        
        foreach ($alterColumnClauses as $alterColumnClause) {
            $sql[] = 'ALTER TABLE ' . $table . ' ' . $alterColumnClause;
        }
    }

    
    private function getAlterColumnClausesSQL(ColumnDiff $columnDiff): array
    {
        $newColumn = $columnDiff->getNewColumn()->toArray();

        $alterClause = 'ALTER COLUMN ' . $columnDiff->getNewColumn()->getQuotedName($this);

        if ($newColumn['columnDefinition'] !== null) {
            return [$alterClause . ' ' . $newColumn['columnDefinition']];
        }

        $clauses = [];

        if (
            $columnDiff->hasTypeChanged() ||
            $columnDiff->hasLengthChanged() ||
            $columnDiff->hasPrecisionChanged() ||
            $columnDiff->hasScaleChanged() ||
            $columnDiff->hasFixedChanged()
        ) {
            $clauses[] = $alterClause . ' SET DATA TYPE ' . $newColumn['type']->getSQLDeclaration($newColumn, $this);
        }

        if ($columnDiff->hasNotNullChanged()) {
            $clauses[] = $newColumn['notnull'] ? $alterClause . ' SET NOT NULL' : $alterClause . ' DROP NOT NULL';
        }

        if ($columnDiff->hasDefaultChanged()) {
            if (isset($newColumn['default'])) {
                $defaultClause = $this->getDefaultValueDeclarationSQL($newColumn);

                if ($defaultClause !== '') {
                    $clauses[] = $alterClause . ' SET' . $defaultClause;
                }
            } else {
                $clauses[] = $alterClause . ' DROP DEFAULT';
            }
        }

        return $clauses;
    }

    
    protected function getPreAlterTableIndexForeignKeySQL(TableDiff $diff)
    {
        $sql = [];

        $tableNameSQL = ($diff->getOldTable() ?? $diff->getName($this))->getQuotedName($this);

        foreach ($diff->getDroppedIndexes() as $droppedIndex) {
            foreach ($diff->getAddedIndexes() as $addedIndex) {
                if ($droppedIndex->getColumns() !== $addedIndex->getColumns()) {
                    continue;
                }

                if ($droppedIndex->isPrimary()) {
                    $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' DROP PRIMARY KEY';
                } elseif ($droppedIndex->isUnique()) {
                    $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' DROP UNIQUE ' . $droppedIndex->getQuotedName($this);
                } else {
                    $sql[] = $this->getDropIndexSQL($droppedIndex, $tableNameSQL);
                }

                $sql[] = $this->getCreateIndexSQL($addedIndex, $tableNameSQL);

                $diff->unsetAddedIndex($addedIndex);
                $diff->unsetDroppedIndex($droppedIndex);

                break;
            }
        }

        return array_merge($sql, parent::getPreAlterTableIndexForeignKeySQL($diff));
    }

    
    protected function getRenameIndexSQL($oldIndexName, Index $index, $tableName)
    {
        if (strpos($tableName, '.') !== false) {
            [$schema]     = explode('.', $tableName);
            $oldIndexName = $schema . '.' . $oldIndexName;
        }

        return ['RENAME INDEX ' . $oldIndexName . ' TO ' . $index->getQuotedName($this)];
    }

    
    public function getDefaultValueDeclarationSQL($column)
    {
        if (! empty($column['autoincrement'])) {
            return '';
        }

        if (! empty($column['version'])) {
            if ((string) $column['type'] !== 'DateTime') {
                $column['default'] = '1';
            }
        }

        return parent::getDefaultValueDeclarationSQL($column);
    }

    
    public function getEmptyIdentityInsertSQL($quotedTableName, $quotedIdentifierColumnName)
    {
        return 'INSERT INTO ' . $quotedTableName . ' (' . $quotedIdentifierColumnName . ') VALUES (DEFAULT)';
    }

    
    public function getCreateTemporaryTableSnippetSQL()
    {
        return 'DECLARE GLOBAL TEMPORARY TABLE';
    }

    
    public function getTemporaryTableName($tableName)
    {
        return 'SESSION.' . $tableName;
    }

    
    protected function doModifyLimitQuery($query, $limit, $offset)
    {
        $where = [];

        if ($offset > 0) {
            $where[] = sprintf('db22.DC_ROWNUM >= %d', $offset + 1);
        }

        if ($limit !== null) {
            $where[] = sprintf('db22.DC_ROWNUM <= %d', $offset + $limit);
        }

        if (empty($where)) {
            return $query;
        }

        
        return sprintf(
            'SELECT db22.* FROM (SELECT db21.*, ROW_NUMBER() OVER() AS DC_ROWNUM FROM (%s) db21) db22 WHERE %s',
            $query,
            implode(' AND ', $where),
        );
    }

    
    public function getLocateExpression($str, $substr, $startPos = false)
    {
        if ($startPos === false) {
            return 'LOCATE(' . $substr . ', ' . $str . ')';
        }

        return 'LOCATE(' . $substr . ', ' . $str . ', ' . $startPos . ')';
    }

    
    public function getSubstringExpression($string, $start, $length = null)
    {
        if ($length === null) {
            return 'SUBSTR(' . $string . ', ' . $start . ')';
        }

        return 'SUBSTR(' . $string . ', ' . $start . ', ' . $length . ')';
    }

    
    public function getLengthExpression($column)
    {
        return 'LENGTH(' . $column . ', CODEUNITS32)';
    }

    public function getCurrentDatabaseExpression(): string
    {
        return 'CURRENT_USER';
    }

    
    public function supportsIdentityColumns()
    {
        return true;
    }

    
    public function prefersIdentityColumns()
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'DB2Platform::prefersIdentityColumns() is deprecated.',
        );

        return true;
    }

    
    public function getForUpdateSQL()
    {
        return ' WITH RR USE AND KEEP UPDATE LOCKS';
    }

    
    public function getDummySelectSQL()
    {
        $expression = func_num_args() > 0 ? func_get_arg(0) : '1';

        return sprintf('SELECT %s FROM sysibm.sysdummy1', $expression);
    }

    
    public function supportsSavepoints()
    {
        return false;
    }

    
    protected function getReservedKeywordsClass()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'DB2Platform::getReservedKeywordsClass() is deprecated,'
                . ' use DB2Platform::createReservedKeywordsList() instead.',
        );

        return Keywords\DB2Keywords::class;
    }

    
    public function getListTableCommentsSQL(string $table): string
    {
        return sprintf(
            <<<'SQL'
SELECT REMARKS
  FROM SYSIBM.SYSTABLES
  WHERE NAME = UPPER( %s )
SQL
            ,
            $this->quoteStringLiteral($table),
        );
    }

    public function createSchemaManager(Connection $connection): DB2SchemaManager
    {
        return new DB2SchemaManager($connection, $this);
    }
}

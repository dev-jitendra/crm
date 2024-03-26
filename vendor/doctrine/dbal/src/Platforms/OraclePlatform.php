<?php

namespace Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Identifier;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\OracleSchemaManager;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\TransactionIsolationLevel;
use Doctrine\DBAL\Types\BinaryType;
use Doctrine\Deprecations\Deprecation;
use InvalidArgumentException;

use function array_merge;
use function count;
use function explode;
use function func_get_arg;
use function func_num_args;
use function implode;
use function preg_match;
use function sprintf;
use function strlen;
use function strpos;
use function strtoupper;
use function substr;


class OraclePlatform extends AbstractPlatform
{
    
    public static function assertValidIdentifier($identifier)
    {
        if (preg_match('(^(([a-zA-Z]{1}[a-zA-Z0-9_$#]{0,})|("[^"]+"))$)', $identifier) === 0) {
            throw new Exception('Invalid Oracle identifier');
        }
    }

    
    public function getSubstringExpression($string, $start, $length = null)
    {
        if ($length !== null) {
            return sprintf('SUBSTR(%s, %d, %d)', $string, $start, $length);
        }

        return sprintf('SUBSTR(%s, %d)', $string, $start);
    }

    
    public function getNowExpression($type = 'timestamp')
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'OraclePlatform::getNowExpression() is deprecated. Generate dates within the application.',
        );

        switch ($type) {
            case 'date':
            case 'time':
            case 'timestamp':
            default:
                return 'TO_CHAR(CURRENT_TIMESTAMP, \'YYYY-MM-DD HH24:MI:SS\')';
        }
    }

    
    public function getLocateExpression($str, $substr, $startPos = false)
    {
        if ($startPos === false) {
            return 'INSTR(' . $str . ', ' . $substr . ')';
        }

        return 'INSTR(' . $str . ', ' . $substr . ', ' . $startPos . ')';
    }

    
    protected function getDateArithmeticIntervalExpression($date, $operator, $interval, $unit)
    {
        switch ($unit) {
            case DateIntervalUnit::MONTH:
            case DateIntervalUnit::QUARTER:
            case DateIntervalUnit::YEAR:
                switch ($unit) {
                    case DateIntervalUnit::QUARTER:
                        $interval *= 3;
                        break;

                    case DateIntervalUnit::YEAR:
                        $interval *= 12;
                        break;
                }

                return 'ADD_MONTHS(' . $date . ', ' . $operator . $interval . ')';

            default:
                $calculationClause = '';

                switch ($unit) {
                    case DateIntervalUnit::SECOND:
                        $calculationClause = '/24/60/60';
                        break;

                    case DateIntervalUnit::MINUTE:
                        $calculationClause = '/24/60';
                        break;

                    case DateIntervalUnit::HOUR:
                        $calculationClause = '/24';
                        break;

                    case DateIntervalUnit::WEEK:
                        $calculationClause = '*7';
                        break;
                }

                return '(' . $date . $operator . $interval . $calculationClause . ')';
        }
    }

    
    public function getDateDiffExpression($date1, $date2)
    {
        return sprintf('TRUNC(%s) - TRUNC(%s)', $date1, $date2);
    }

    
    public function getBitAndComparisonExpression($value1, $value2)
    {
        return 'BITAND(' . $value1 . ', ' . $value2 . ')';
    }

    public function getCurrentDatabaseExpression(): string
    {
        return "SYS_CONTEXT('USERENV', 'CURRENT_SCHEMA')";
    }

    
    public function getBitOrComparisonExpression($value1, $value2)
    {
        return '(' . $value1 . '-' .
                $this->getBitAndComparisonExpression($value1, $value2)
                . '+' . $value2 . ')';
    }

    
    public function getCreatePrimaryKeySQL(Index $index, $table): string
    {
        if ($table instanceof Table) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.',
                __METHOD__,
            );

            $table = $table->getQuotedName($this);
        }

        return 'ALTER TABLE ' . $table . ' ADD CONSTRAINT ' . $index->getQuotedName($this)
            . ' PRIMARY KEY (' . $this->getIndexFieldDeclarationListSQL($index) . ')';
    }

    
    public function getCreateSequenceSQL(Sequence $sequence)
    {
        return 'CREATE SEQUENCE ' . $sequence->getQuotedName($this) .
               ' START WITH ' . $sequence->getInitialValue() .
               ' MINVALUE ' . $sequence->getInitialValue() .
               ' INCREMENT BY ' . $sequence->getAllocationSize() .
               $this->getSequenceCacheSQL($sequence);
    }

    
    public function getAlterSequenceSQL(Sequence $sequence)
    {
        return 'ALTER SEQUENCE ' . $sequence->getQuotedName($this) .
               ' INCREMENT BY ' . $sequence->getAllocationSize()
               . $this->getSequenceCacheSQL($sequence);
    }

    
    private function getSequenceCacheSQL(Sequence $sequence): string
    {
        if ($sequence->getCache() === 0) {
            return ' NOCACHE';
        }

        if ($sequence->getCache() === 1) {
            return ' NOCACHE';
        }

        if ($sequence->getCache() > 1) {
            return ' CACHE ' . $sequence->getCache();
        }

        return '';
    }

    
    public function getSequenceNextValSQL($sequence)
    {
        return 'SELECT ' . $sequence . '.nextval FROM DUAL';
    }

    
    public function getSetTransactionIsolationSQL($level)
    {
        return 'SET TRANSACTION ISOLATION LEVEL ' . $this->_getTransactionIsolationLevelSQL($level);
    }

    
    protected function _getTransactionIsolationLevelSQL($level)
    {
        switch ($level) {
            case TransactionIsolationLevel::READ_UNCOMMITTED:
                return 'READ UNCOMMITTED';

            case TransactionIsolationLevel::READ_COMMITTED:
                return 'READ COMMITTED';

            case TransactionIsolationLevel::REPEATABLE_READ:
            case TransactionIsolationLevel::SERIALIZABLE:
                return 'SERIALIZABLE';

            default:
                return parent::_getTransactionIsolationLevelSQL($level);
        }
    }

    
    public function getBooleanTypeDeclarationSQL(array $column)
    {
        return 'NUMBER(1)';
    }

    
    public function getIntegerTypeDeclarationSQL(array $column)
    {
        return 'NUMBER(10)';
    }

    
    public function getBigIntTypeDeclarationSQL(array $column)
    {
        return 'NUMBER(20)';
    }

    
    public function getSmallIntTypeDeclarationSQL(array $column)
    {
        return 'NUMBER(5)';
    }

    
    public function getDateTimeTypeDeclarationSQL(array $column)
    {
        return 'TIMESTAMP(0)';
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
        return 'DATE';
    }

    
    protected function _getCommonIntegerTypeDeclarationSQL(array $column)
    {
        return '';
    }

    
    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed)
    {
        if ($length <= 0 || (func_num_args() > 2 && func_get_arg(2))) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Relying on the default string column length on Oracle is deprecated'
                    . ', specify the length explicitly.',
            );
        }

        return $fixed ? ($length > 0 ? 'CHAR(' . $length . ')' : 'CHAR(2000)')
                : ($length > 0 ? 'VARCHAR2(' . $length . ')' : 'VARCHAR2(4000)');
    }

    
    protected function getBinaryTypeDeclarationSQLSnippet($length, $fixed)
    {
        if ($length <= 0 || (func_num_args() > 2 && func_get_arg(2))) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Relying on the default binary column length on Oracle is deprecated'
                . ', specify the length explicitly.',
            );
        }

        return 'RAW(' . ($length > 0 ? $length : $this->getBinaryMaxLength()) . ')';
    }

    
    public function getBinaryMaxLength()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'OraclePlatform::getBinaryMaxLength() is deprecated.',
        );

        return 2000;
    }

    
    public function getClobTypeDeclarationSQL(array $column)
    {
        return 'CLOB';
    }

    
    public function getListDatabasesSQL()
    {
        return 'SELECT username FROM all_users';
    }

    
    public function getListSequencesSQL($database)
    {
        $database = $this->normalizeIdentifier($database);
        $database = $this->quoteStringLiteral($database->getName());

        return 'SELECT sequence_name, min_value, increment_by FROM sys.all_sequences ' .
               'WHERE SEQUENCE_OWNER = ' . $database;
    }

    
    protected function _getCreateTableSQL($name, array $columns, array $options = [])
    {
        $indexes            = $options['indexes'] ?? [];
        $options['indexes'] = [];
        $sql                = parent::_getCreateTableSQL($name, $columns, $options);

        foreach ($columns as $columnName => $column) {
            if (isset($column['sequence'])) {
                $sql[] = $this->getCreateSequenceSQL($column['sequence']);
            }

            if (
                ! isset($column['autoincrement']) || ! $column['autoincrement'] &&
                (! isset($column['autoinc']) || ! $column['autoinc'])
            ) {
                continue;
            }

            $sql = array_merge($sql, $this->getCreateAutoincrementSql($columnName, $name));
        }

        foreach ($indexes as $index) {
            $sql[] = $this->getCreateIndexSQL($index, $name);
        }

        return $sql;
    }

    
    public function getListTableIndexesSQL($table, $database = null)
    {
        $table = $this->normalizeIdentifier($table);
        $table = $this->quoteStringLiteral($table->getName());

        return "SELECT uind_col.index_name AS name,
                       (
                           SELECT uind.index_type
                           FROM   user_indexes uind
                           WHERE  uind.index_name = uind_col.index_name
                       ) AS type,
                       decode(
                           (
                               SELECT uind.uniqueness
                               FROM   user_indexes uind
                               WHERE  uind.index_name = uind_col.index_name
                           ),
                           'NONUNIQUE',
                           0,
                           'UNIQUE',
                           1
                       ) AS is_unique,
                       uind_col.column_name AS column_name,
                       uind_col.column_position AS column_pos,
                       (
                           SELECT ucon.constraint_type
                           FROM   user_constraints ucon
                           WHERE  ucon.index_name = uind_col.index_name
                             AND  ucon.table_name = uind_col.table_name
                       ) AS is_primary
             FROM      user_ind_columns uind_col
             WHERE     uind_col.table_name = " . $table . '
             ORDER BY  uind_col.column_position ASC';
    }

    
    public function getListTablesSQL()
    {
        return 'SELECT * FROM sys.user_tables';
    }

    
    public function getListViewsSQL($database)
    {
        return 'SELECT view_name, text FROM sys.user_views';
    }

    
    public function getCreateAutoincrementSql($name, $table, $start = 1)
    {
        $tableIdentifier   = $this->normalizeIdentifier($table);
        $quotedTableName   = $tableIdentifier->getQuotedName($this);
        $unquotedTableName = $tableIdentifier->getName();

        $nameIdentifier = $this->normalizeIdentifier($name);
        $quotedName     = $nameIdentifier->getQuotedName($this);
        $unquotedName   = $nameIdentifier->getName();

        $sql = [];

        $autoincrementIdentifierName = $this->getAutoincrementIdentifierName($tableIdentifier);

        $idx = new Index($autoincrementIdentifierName, [$quotedName], true, true);

        $sql[] = "DECLARE
  constraints_Count NUMBER;
BEGIN
  SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count
    FROM USER_CONSTRAINTS
   WHERE TABLE_NAME = '" . $unquotedTableName . "'
     AND CONSTRAINT_TYPE = 'P';
  IF constraints_Count = 0 OR constraints_Count = '' THEN
    EXECUTE IMMEDIATE '" . $this->getCreateConstraintSQL($idx, $quotedTableName) . "';
  END IF;
END;";

        $sequenceName = $this->getIdentitySequenceName(
            $tableIdentifier->isQuoted() ? $quotedTableName : $unquotedTableName,
            $nameIdentifier->isQuoted() ? $quotedName : $unquotedName,
        );
        $sequence     = new Sequence($sequenceName, $start);
        $sql[]        = $this->getCreateSequenceSQL($sequence);

        $sql[] = 'CREATE TRIGGER ' . $autoincrementIdentifierName . '
   BEFORE INSERT
   ON ' . $quotedTableName . '
   FOR EACH ROW
DECLARE
   last_Sequence NUMBER;
   last_InsertID NUMBER;
BEGIN
   IF (:NEW.' . $quotedName . ' IS NULL OR :NEW.' . $quotedName . ' = 0) THEN
      SELECT ' . $sequenceName . '.NEXTVAL INTO :NEW.' . $quotedName . ' FROM DUAL;
   ELSE
      SELECT NVL(Last_Number, 0) INTO last_Sequence
        FROM User_Sequences
       WHERE Sequence_Name = \'' . $sequence->getName() . '\';
      SELECT :NEW.' . $quotedName . ' INTO last_InsertID FROM DUAL;
      WHILE (last_InsertID > last_Sequence) LOOP
         SELECT ' . $sequenceName . '.NEXTVAL INTO last_Sequence FROM DUAL;
      END LOOP;
      SELECT ' . $sequenceName . '.NEXTVAL INTO last_Sequence FROM DUAL;
   END IF;
END;';

        return $sql;
    }

    
    public function getDropAutoincrementSql($table)
    {
        $table                       = $this->normalizeIdentifier($table);
        $autoincrementIdentifierName = $this->getAutoincrementIdentifierName($table);
        $identitySequenceName        = $this->getIdentitySequenceName(
            $table->isQuoted() ? $table->getQuotedName($this) : $table->getName(),
            '',
        );

        return [
            'DROP TRIGGER ' . $autoincrementIdentifierName,
            $this->getDropSequenceSQL($identitySequenceName),
            $this->getDropConstraintSQL($autoincrementIdentifierName, $table->getQuotedName($this)),
        ];
    }

    
    private function normalizeIdentifier($name): Identifier
    {
        $identifier = new Identifier($name);

        return $identifier->isQuoted() ? $identifier : new Identifier(strtoupper($name));
    }

    
    private function addSuffix(string $identifier, string $suffix): string
    {
        $maxPossibleLengthWithoutSuffix = $this->getMaxIdentifierLength() - strlen($suffix);
        if (strlen($identifier) > $maxPossibleLengthWithoutSuffix) {
            $identifier = substr($identifier, 0, $maxPossibleLengthWithoutSuffix);
        }

        return $identifier . $suffix;
    }

    
    private function getAutoincrementIdentifierName(Identifier $table): string
    {
        $identifierName = $this->addSuffix($table->getName(), '_AI_PK');

        return $table->isQuoted()
            ? $this->quoteSingleIdentifier($identifierName)
            : $identifierName;
    }

    
    public function getListTableForeignKeysSQL($table)
    {
        $table = $this->normalizeIdentifier($table);
        $table = $this->quoteStringLiteral($table->getName());

        return "SELECT alc.constraint_name,
          alc.DELETE_RULE,
          cols.column_name \"local_column\",
          cols.position,
          (
              SELECT r_cols.table_name
              FROM   user_cons_columns r_cols
              WHERE  alc.r_constraint_name = r_cols.constraint_name
              AND    r_cols.position = cols.position
          ) AS \"references_table\",
          (
              SELECT r_cols.column_name
              FROM   user_cons_columns r_cols
              WHERE  alc.r_constraint_name = r_cols.constraint_name
              AND    r_cols.position = cols.position
          ) AS \"foreign_column\"
     FROM user_cons_columns cols
     JOIN user_constraints alc
       ON alc.constraint_name = cols.constraint_name
      AND alc.constraint_type = 'R'
      AND alc.table_name = " . $table . '
    ORDER BY cols.constraint_name ASC, cols.position ASC';
    }

    
    public function getListTableConstraintsSQL($table)
    {
        $table = $this->normalizeIdentifier($table);
        $table = $this->quoteStringLiteral($table->getName());

        return 'SELECT * FROM user_constraints WHERE table_name = ' . $table;
    }

    
    public function getListTableColumnsSQL($table, $database = null)
    {
        $table = $this->normalizeIdentifier($table);
        $table = $this->quoteStringLiteral($table->getName());

        $tabColumnsTableName       = 'user_tab_columns';
        $colCommentsTableName      = 'user_col_comments';
        $tabColumnsOwnerCondition  = '';
        $colCommentsOwnerCondition = '';

        if ($database !== null && $database !== '/') {
            $database                  = $this->normalizeIdentifier($database);
            $database                  = $this->quoteStringLiteral($database->getName());
            $tabColumnsTableName       = 'all_tab_columns';
            $colCommentsTableName      = 'all_col_comments';
            $tabColumnsOwnerCondition  = ' AND c.owner = ' . $database;
            $colCommentsOwnerCondition = ' AND d.OWNER = c.OWNER';
        }

        return sprintf(
            <<<'SQL'
SELECT   c.*,
         (
             SELECT d.comments
             FROM   %s d
             WHERE  d.TABLE_NAME = c.TABLE_NAME%s
             AND    d.COLUMN_NAME = c.COLUMN_NAME
         ) AS comments
FROM     %s c
WHERE    c.table_name = %s%s
ORDER BY c.column_id
SQL
            ,
            $colCommentsTableName,
            $colCommentsOwnerCondition,
            $tabColumnsTableName,
            $table,
            $tabColumnsOwnerCondition,
        );
    }

    
    public function getDropForeignKeySQL($foreignKey, $table)
    {
        if ($foreignKey instanceof ForeignKeyConstraint) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Passing $foreignKey as a ForeignKeyConstraint object to %s is deprecated.'
                . ' Pass it as a quoted name instead.',
                __METHOD__,
            );
        } else {
            $foreignKey = new Identifier($foreignKey);
        }

        if ($table instanceof Table) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.',
                __METHOD__,
            );
        } else {
            $table = new Identifier($table);
        }

        $foreignKey = $foreignKey->getQuotedName($this);
        $table      = $table->getQuotedName($this);

        return 'ALTER TABLE ' . $table . ' DROP CONSTRAINT ' . $foreignKey;
    }

    
    public function getAdvancedForeignKeyOptionsSQL(ForeignKeyConstraint $foreignKey)
    {
        $referentialAction = '';

        if ($foreignKey->hasOption('onDelete')) {
            $referentialAction = $this->getForeignKeyReferentialActionSQL($foreignKey->getOption('onDelete'));
        }

        if ($referentialAction !== '') {
            return ' ON DELETE ' . $referentialAction;
        }

        return '';
    }

    
    public function getForeignKeyReferentialActionSQL($action)
    {
        $action = strtoupper($action);

        switch ($action) {
            case 'RESTRICT': 
            case 'NO ACTION':
                
                
                return '';

            case 'CASCADE':
            case 'SET NULL':
                return $action;

            default:
                
                throw new InvalidArgumentException('Invalid foreign key action: ' . $action);
        }
    }

    
    public function getCreateDatabaseSQL($name)
    {
        return 'CREATE USER ' . $name;
    }

    
    public function getDropDatabaseSQL($name)
    {
        return 'DROP USER ' . $name . ' CASCADE';
    }

    
    public function getAlterTableSQL(TableDiff $diff)
    {
        $sql         = [];
        $commentsSQL = [];
        $columnSql   = [];

        $fields = [];

        $tableNameSQL = ($diff->getOldTable() ?? $diff->getName($this))->getQuotedName($this);

        foreach ($diff->getAddedColumns() as $column) {
            if ($this->onSchemaAlterTableAddColumn($column, $diff, $columnSql)) {
                continue;
            }

            $fields[] = $this->getColumnDeclarationSQL($column->getQuotedName($this), $column->toArray());
            $comment  = $this->getColumnComment($column);

            if ($comment === null || $comment === '') {
                continue;
            }

            $commentsSQL[] = $this->getCommentOnColumnSQL(
                $tableNameSQL,
                $column->getQuotedName($this),
                $comment,
            );
        }

        if (count($fields) > 0) {
            $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' ADD (' . implode(', ', $fields) . ')';
        }

        $fields = [];
        foreach ($diff->getModifiedColumns() as $columnDiff) {
            if ($this->onSchemaAlterTableChangeColumn($columnDiff, $diff, $columnSql)) {
                continue;
            }

            $newColumn = $columnDiff->getNewColumn();

            
            
            
            if (
                $newColumn->getType() instanceof BinaryType &&
                $columnDiff->hasFixedChanged() &&
                count($columnDiff->changedProperties) === 1
            ) {
                continue;
            }

            $columnHasChangedComment = $columnDiff->hasCommentChanged();

            
            if (! ($columnHasChangedComment && count($columnDiff->changedProperties) === 1)) {
                $newColumnProperties = $newColumn->toArray();

                if (! $columnDiff->hasNotNullChanged()) {
                    unset($newColumnProperties['notnull']);
                }

                $fields[] = $newColumn->getQuotedName($this) . $this->getColumnDeclarationSQL('', $newColumnProperties);
            }

            if (! $columnHasChangedComment) {
                continue;
            }

            $commentsSQL[] = $this->getCommentOnColumnSQL(
                $tableNameSQL,
                $newColumn->getQuotedName($this),
                $this->getColumnComment($newColumn),
            );
        }

        if (count($fields) > 0) {
            $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' MODIFY (' . implode(', ', $fields) . ')';
        }

        foreach ($diff->getRenamedColumns() as $oldColumnName => $column) {
            if ($this->onSchemaAlterTableRenameColumn($oldColumnName, $column, $diff, $columnSql)) {
                continue;
            }

            $oldColumnName = new Identifier($oldColumnName);

            $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' RENAME COLUMN ' . $oldColumnName->getQuotedName($this)
                . ' TO ' . $column->getQuotedName($this);
        }

        $fields = [];
        foreach ($diff->getDroppedColumns() as $column) {
            if ($this->onSchemaAlterTableRemoveColumn($column, $diff, $columnSql)) {
                continue;
            }

            $fields[] = $column->getQuotedName($this);
        }

        if (count($fields) > 0) {
            $sql[] = 'ALTER TABLE ' . $tableNameSQL . ' DROP (' . implode(', ', $fields) . ')';
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

    
    public function getColumnDeclarationSQL($name, array $column)
    {
        if (isset($column['columnDefinition'])) {
            $columnDef = $this->getCustomTypeDeclarationSQL($column);
        } else {
            $default = $this->getDefaultValueDeclarationSQL($column);

            $notnull = '';

            if (isset($column['notnull'])) {
                $notnull = $column['notnull'] ? ' NOT NULL' : ' NULL';
            }

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
            $columnDef = $typeDecl . $default . $notnull . $unique . $check;
        }

        return $name . ' ' . $columnDef;
    }

    
    protected function getRenameIndexSQL($oldIndexName, Index $index, $tableName)
    {
        if (strpos($tableName, '.') !== false) {
            [$schema]     = explode('.', $tableName);
            $oldIndexName = $schema . '.' . $oldIndexName;
        }

        return ['ALTER INDEX ' . $oldIndexName . ' RENAME TO ' . $index->getQuotedName($this)];
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
        $table = new Identifier($tableName);

        
        $identitySequenceName = $this->addSuffix($table->getName(), '_SEQ');

        if ($table->isQuoted()) {
            $identitySequenceName = '"' . $identitySequenceName . '"';
        }

        $identitySequenceIdentifier = $this->normalizeIdentifier($identitySequenceName);

        return $identitySequenceIdentifier->getQuotedName($this);
    }

    
    public function supportsCommentOnStatement()
    {
        return true;
    }

    
    public function getName()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'OraclePlatform::getName() is deprecated. Identify platforms by their class.',
        );

        return 'oracle';
    }

    
    protected function doModifyLimitQuery($query, $limit, $offset)
    {
        if ($limit === null && $offset <= 0) {
            return $query;
        }

        if (preg_match('/^\s*SELECT/i', $query) === 1) {
            if (preg_match('/\sFROM\s/i', $query) === 0) {
                $query .= ' FROM dual';
            }

            $columns = ['a.*'];

            if ($offset > 0) {
                $columns[] = 'ROWNUM AS doctrine_rownum';
            }

            $query = sprintf('SELECT %s FROM (%s) a', implode(', ', $columns), $query);

            if ($limit !== null) {
                $query .= sprintf(' WHERE ROWNUM <= %d', $offset + $limit);
            }

            if ($offset > 0) {
                $query = sprintf('SELECT * FROM (%s) WHERE doctrine_rownum >= %d', $query, $offset + 1);
            }
        }

        return $query;
    }

    
    public function getCreateTemporaryTableSnippetSQL()
    {
        return 'CREATE GLOBAL TEMPORARY TABLE';
    }

    
    public function getDateTimeTzFormatString()
    {
        return 'Y-m-d H:i:sP';
    }

    
    public function getDateFormatString()
    {
        return 'Y-m-d 00:00:00';
    }

    
    public function getTimeFormatString()
    {
        return '1900-01-01 H:i:s';
    }

    
    public function getMaxIdentifierLength()
    {
        return 30;
    }

    
    public function supportsSequences()
    {
        return true;
    }

    
    public function supportsReleaseSavepoints()
    {
        return false;
    }

    
    public function getTruncateTableSQL($tableName, $cascade = false)
    {
        $tableIdentifier = new Identifier($tableName);

        return 'TRUNCATE TABLE ' . $tableIdentifier->getQuotedName($this);
    }

    
    public function getDummySelectSQL()
    {
        $expression = func_num_args() > 0 ? func_get_arg(0) : '1';

        return sprintf('SELECT %s FROM DUAL', $expression);
    }

    
    protected function initializeDoctrineTypeMappings()
    {
        $this->doctrineTypeMapping = [
            'binary_double'  => 'float',
            'binary_float'   => 'float',
            'binary_integer' => 'boolean',
            'blob'           => 'blob',
            'char'           => 'string',
            'clob'           => 'text',
            'date'           => 'date',
            'float'          => 'float',
            'integer'        => 'integer',
            'long'           => 'string',
            'long raw'       => 'blob',
            'nchar'          => 'string',
            'nclob'          => 'text',
            'number'         => 'integer',
            'nvarchar2'      => 'string',
            'pls_integer'    => 'boolean',
            'raw'            => 'binary',
            'rowid'          => 'string',
            'timestamp'      => 'datetime',
            'timestamptz'    => 'datetimetz',
            'urowid'         => 'string',
            'varchar'        => 'string',
            'varchar2'       => 'string',
        ];
    }

    
    public function releaseSavePoint($savepoint)
    {
        return '';
    }

    
    protected function getReservedKeywordsClass()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'OraclePlatform::getReservedKeywordsClass() is deprecated,'
            . ' use OraclePlatform::createReservedKeywordsList() instead.',
        );

        return Keywords\OracleKeywords::class;
    }

    
    public function getBlobTypeDeclarationSQL(array $column)
    {
        return 'BLOB';
    }

    
    public function getListTableCommentsSQL(string $table, ?string $database = null): string
    {
        $tableCommentsName = 'user_tab_comments';
        $ownerCondition    = '';

        if ($database !== null && $database !== '/') {
            $tableCommentsName = 'all_tab_comments';
            $ownerCondition    = ' AND owner = ' . $this->quoteStringLiteral(
                $this->normalizeIdentifier($database)->getName(),
            );
        }

        return sprintf(
            <<<'SQL'
SELECT comments FROM %s WHERE table_name = %s%s
SQL
            ,
            $tableCommentsName,
            $this->quoteStringLiteral($this->normalizeIdentifier($table)->getName()),
            $ownerCondition,
        );
    }

    public function createSchemaManager(Connection $connection): OracleSchemaManager
    {
        return new OracleSchemaManager($connection, $this);
    }
}

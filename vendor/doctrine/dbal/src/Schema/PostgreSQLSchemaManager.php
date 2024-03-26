<?php

namespace Doctrine\DBAL\Schema;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\Deprecations\Deprecation;

use function array_change_key_case;
use function array_filter;
use function array_map;
use function array_merge;
use function array_shift;
use function assert;
use function explode;
use function get_class;
use function implode;
use function in_array;
use function preg_match;
use function preg_replace;
use function sprintf;
use function str_replace;
use function strpos;
use function strtolower;
use function trim;

use const CASE_LOWER;


class PostgreSQLSchemaManager extends AbstractSchemaManager
{
    
    private ?array $existingSchemaPaths = null;

    
    public function listTableNames()
    {
        return $this->doListTableNames();
    }

    
    public function listTables()
    {
        return $this->doListTables();
    }

    
    public function listTableDetails($name)
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            '%s is deprecated. Use introspectTable() instead.',
            __METHOD__,
        );

        return $this->doListTableDetails($name);
    }

    
    public function listTableColumns($table, $database = null)
    {
        return $this->doListTableColumns($table, $database);
    }

    
    public function listTableIndexes($table)
    {
        return $this->doListTableIndexes($table);
    }

    
    public function listTableForeignKeys($table, $database = null)
    {
        return $this->doListTableForeignKeys($table, $database);
    }

    
    public function getSchemaNames()
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'PostgreSQLSchemaManager::getSchemaNames() is deprecated,'
                . ' use PostgreSQLSchemaManager::listSchemaNames() instead.',
        );

        return $this->listNamespaceNames();
    }

    
    public function listSchemaNames(): array
    {
        return $this->_conn->fetchFirstColumn(
            <<<'SQL'
SELECT schema_name
FROM   information_schema.schemata
WHERE  schema_name NOT LIKE 'pg\_%'
AND    schema_name != 'information_schema'
SQL,
        );
    }

    
    public function getSchemaSearchPaths()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'PostgreSQLSchemaManager::getSchemaSearchPaths() is deprecated.',
        );

        $params = $this->_conn->getParams();

        $searchPaths = $this->_conn->fetchOne('SHOW search_path');
        assert($searchPaths !== false);

        $schema = explode(',', $searchPaths);

        if (isset($params['user'])) {
            $schema = str_replace('"$user"', $params['user'], $schema);
        }

        return array_map('trim', $schema);
    }

    
    public function getExistingSchemaSearchPaths()
    {
        if ($this->existingSchemaPaths === null) {
            $this->determineExistingSchemaSearchPaths();
        }

        assert($this->existingSchemaPaths !== null);

        return $this->existingSchemaPaths;
    }

    
    protected function getCurrentSchema()
    {
        $schemas = $this->getExistingSchemaSearchPaths();

        return array_shift($schemas);
    }

    
    public function determineExistingSchemaSearchPaths()
    {
        $names = $this->listSchemaNames();
        $paths = $this->getSchemaSearchPaths();

        $this->existingSchemaPaths = array_filter($paths, static function ($v) use ($names): bool {
            return in_array($v, $names, true);
        });
    }

    
    protected function _getPortableTableForeignKeyDefinition($tableForeignKey)
    {
        $onUpdate = null;
        $onDelete = null;

        if (
            preg_match(
                '(ON UPDATE ([a-zA-Z0-9]+( (NULL|ACTION|DEFAULT))?))',
                $tableForeignKey['condef'],
                $match,
            ) === 1
        ) {
            $onUpdate = $match[1];
        }

        if (
            preg_match(
                '(ON DELETE ([a-zA-Z0-9]+( (NULL|ACTION|DEFAULT))?))',
                $tableForeignKey['condef'],
                $match,
            ) === 1
        ) {
            $onDelete = $match[1];
        }

        $result = preg_match('/FOREIGN KEY \((.+)\) REFERENCES (.+)\((.+)\)/', $tableForeignKey['condef'], $values);
        assert($result === 1);

        
        
        $localColumns   = array_map('trim', explode(',', $values[1]));
        $foreignColumns = array_map('trim', explode(',', $values[3]));
        $foreignTable   = $values[2];

        return new ForeignKeyConstraint(
            $localColumns,
            $foreignTable,
            $foreignColumns,
            $tableForeignKey['conname'],
            ['onUpdate' => $onUpdate, 'onDelete' => $onDelete],
        );
    }

    
    protected function _getPortableViewDefinition($view)
    {
        return new View($view['schemaname'] . '.' . $view['viewname'], $view['definition']);
    }

    
    protected function _getPortableTableDefinition($table)
    {
        $currentSchema = $this->getCurrentSchema();

        if ($table['schema_name'] === $currentSchema) {
            return $table['table_name'];
        }

        return $table['schema_name'] . '.' . $table['table_name'];
    }

    
    protected function _getPortableTableIndexesList($tableIndexes, $tableName = null)
    {
        $buffer = [];
        foreach ($tableIndexes as $row) {
            $colNumbers    = array_map('intval', explode(' ', $row['indkey']));
            $columnNameSql = sprintf(
                'SELECT attnum, attname FROM pg_attribute WHERE attrelid=%d AND attnum IN (%s) ORDER BY attnum ASC',
                $row['indrelid'],
                implode(' ,', $colNumbers),
            );

            $indexColumns = $this->_conn->fetchAllAssociative($columnNameSql);

            
            foreach ($colNumbers as $colNum) {
                foreach ($indexColumns as $colRow) {
                    if ($colNum !== $colRow['attnum']) {
                        continue;
                    }

                    $buffer[] = [
                        'key_name' => $row['relname'],
                        'column_name' => trim($colRow['attname']),
                        'non_unique' => ! $row['indisunique'],
                        'primary' => $row['indisprimary'],
                        'where' => $row['where'],
                    ];
                }
            }
        }

        return parent::_getPortableTableIndexesList($buffer, $tableName);
    }

    
    protected function _getPortableDatabaseDefinition($database)
    {
        return $database['datname'];
    }

    
    protected function getPortableNamespaceDefinition(array $namespace)
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'PostgreSQLSchemaManager::getPortableNamespaceDefinition() is deprecated,'
                . ' use PostgreSQLSchemaManager::listSchemaNames() instead.',
        );

        return $namespace['nspname'];
    }

    
    protected function _getPortableSequenceDefinition($sequence)
    {
        if ($sequence['schemaname'] !== 'public') {
            $sequenceName = $sequence['schemaname'] . '.' . $sequence['relname'];
        } else {
            $sequenceName = $sequence['relname'];
        }

        return new Sequence($sequenceName, (int) $sequence['increment_by'], (int) $sequence['min_value']);
    }

    
    protected function _getPortableTableColumnDefinition($tableColumn)
    {
        $tableColumn = array_change_key_case($tableColumn, CASE_LOWER);

        if (strtolower($tableColumn['type']) === 'varchar' || strtolower($tableColumn['type']) === 'bpchar') {
            
            $length                = preg_replace('~.*\(([0-9]*)\).*~', '$1', $tableColumn['complete_type']);
            $tableColumn['length'] = $length;
        }

        $matches = [];

        $autoincrement = false;

        if (
            $tableColumn['default'] !== null
            && preg_match("/^nextval\('(.*)'(::.*)?\)$/", $tableColumn['default'], $matches) === 1
        ) {
            $tableColumn['sequence'] = $matches[1];
            $tableColumn['default']  = null;
            $autoincrement           = true;
        }

        if ($tableColumn['default'] !== null) {
            if (preg_match("/^['(](.*)[')]::/", $tableColumn['default'], $matches) === 1) {
                $tableColumn['default'] = $matches[1];
            } elseif (preg_match('/^NULL::/', $tableColumn['default']) === 1) {
                $tableColumn['default'] = null;
            }
        }

        $length = $tableColumn['length'] ?? null;
        if ($length === '-1' && isset($tableColumn['atttypmod'])) {
            $length = $tableColumn['atttypmod'] - 4;
        }

        if ((int) $length <= 0) {
            $length = null;
        }

        $fixed = null;

        if (! isset($tableColumn['name'])) {
            $tableColumn['name'] = '';
        }

        $precision = null;
        $scale     = null;
        $jsonb     = null;

        $dbType = strtolower($tableColumn['type']);
        if (
            $tableColumn['domain_type'] !== null
            && $tableColumn['domain_type'] !== ''
            && ! $this->_platform->hasDoctrineTypeMappingFor($tableColumn['type'])
        ) {
            $dbType                       = strtolower($tableColumn['domain_type']);
            $tableColumn['complete_type'] = $tableColumn['domain_complete_type'];
        }

        $type                   = $this->_platform->getDoctrineTypeMapping($dbType);
        $type                   = $this->extractDoctrineTypeFromComment($tableColumn['comment'], $type);
        $tableColumn['comment'] = $this->removeDoctrineTypeFromComment($tableColumn['comment'], $type);

        switch ($dbType) {
            case 'smallint':
            case 'int2':
                $tableColumn['default'] = $this->fixVersion94NegativeNumericDefaultValue($tableColumn['default']);
                $length                 = null;
                break;

            case 'int':
            case 'int4':
            case 'integer':
                $tableColumn['default'] = $this->fixVersion94NegativeNumericDefaultValue($tableColumn['default']);
                $length                 = null;
                break;

            case 'bigint':
            case 'int8':
                $tableColumn['default'] = $this->fixVersion94NegativeNumericDefaultValue($tableColumn['default']);
                $length                 = null;
                break;

            case 'bool':
            case 'boolean':
                if ($tableColumn['default'] === 'true') {
                    $tableColumn['default'] = true;
                }

                if ($tableColumn['default'] === 'false') {
                    $tableColumn['default'] = false;
                }

                $length = null;
                break;

            case 'text':
            case '_varchar':
            case 'varchar':
                $tableColumn['default'] = $this->parseDefaultExpression($tableColumn['default']);
                $fixed                  = false;
                break;
            case 'interval':
                $fixed = false;
                break;

            case 'char':
            case 'bpchar':
                $fixed = true;
                break;

            case 'float':
            case 'float4':
            case 'float8':
            case 'double':
            case 'double precision':
            case 'real':
            case 'decimal':
            case 'money':
            case 'numeric':
                $tableColumn['default'] = $this->fixVersion94NegativeNumericDefaultValue($tableColumn['default']);

                if (
                    preg_match(
                        '([A-Za-z]+\(([0-9]+),([0-9]+)\))',
                        $tableColumn['complete_type'],
                        $match,
                    ) === 1
                ) {
                    $precision = $match[1];
                    $scale     = $match[2];
                    $length    = null;
                }

                break;

            case 'year':
                $length = null;
                break;

            
            case 'jsonb':
                $jsonb = true;
                break;
        }

        if (
            $tableColumn['default'] !== null && preg_match(
                "('([^']+)'::)",
                $tableColumn['default'],
                $match,
            ) === 1
        ) {
            $tableColumn['default'] = $match[1];
        }

        $options = [
            'length'        => $length,
            'notnull'       => (bool) $tableColumn['isnotnull'],
            'default'       => $tableColumn['default'],
            'precision'     => $precision,
            'scale'         => $scale,
            'fixed'         => $fixed,
            'unsigned'      => false,
            'autoincrement' => $autoincrement,
            'comment'       => isset($tableColumn['comment']) && $tableColumn['comment'] !== ''
                ? $tableColumn['comment']
                : null,
        ];

        $column = new Column($tableColumn['field'], Type::getType($type), $options);

        if (isset($tableColumn['collation']) && ! empty($tableColumn['collation'])) {
            $column->setPlatformOption('collation', $tableColumn['collation']);
        }

        if ($column->getType()->getName() === Types::JSON) {
            if (! $column->getType() instanceof JsonType) {
                Deprecation::trigger(
                    'doctrine/dbal',
                    'https:
                    <<<'DEPRECATION'
                    %s not extending %s while being named %s is deprecated,
                    and will lead to jsonb never to being used in 4.0.,
                    DEPRECATION,
                    get_class($column->getType()),
                    JsonType::class,
                    Types::JSON,
                );
            }

            $column->setPlatformOption('jsonb', $jsonb);
        }

        return $column;
    }

    
    private function fixVersion94NegativeNumericDefaultValue($defaultValue)
    {
        if ($defaultValue !== null && strpos($defaultValue, '(') === 0) {
            return trim($defaultValue, '()');
        }

        return $defaultValue;
    }

    
    private function parseDefaultExpression(?string $default): ?string
    {
        if ($default === null) {
            return $default;
        }

        return str_replace("''", "'", $default);
    }

    protected function selectTableNames(string $databaseName): Result
    {
        $sql = <<<'SQL'
SELECT quote_ident(table_name) AS table_name,
       table_schema AS schema_name
FROM information_schema.tables
WHERE table_catalog = ?
  AND table_schema NOT LIKE 'pg\_%'
  AND table_schema != 'information_schema'
  AND table_name != 'geometry_columns'
  AND table_name != 'spatial_ref_sys'
  AND table_type = 'BASE TABLE'
SQL;

        return $this->_conn->executeQuery($sql, [$databaseName]);
    }

    protected function selectTableColumns(string $databaseName, ?string $tableName = null): Result
    {
        $sql = 'SELECT';

        if ($tableName === null) {
            $sql .= ' c.relname AS table_name, n.nspname AS schema_name,';
        }

        $sql .= <<<'SQL'
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
            FROM pg_attribute a
                INNER JOIN pg_class c
                    ON c.oid = a.attrelid
                INNER JOIN pg_type t
                    ON t.oid = a.atttypid
                INNER JOIN pg_namespace n
                    ON n.oid = c.relnamespace
                LEFT JOIN pg_depend d
                    ON d.objid = c.oid
                        AND d.deptype = 'e'
SQL;

        $conditions = array_merge([
            'a.attnum > 0',
            "c.relkind = 'r'",
            'd.refobjid IS NULL',
        ], $this->buildQueryConditions($tableName));

        $sql .= ' WHERE ' . implode(' AND ', $conditions) . ' ORDER BY a.attnum';

        return $this->_conn->executeQuery($sql);
    }

    protected function selectIndexColumns(string $databaseName, ?string $tableName = null): Result
    {
        $sql = 'SELECT';

        if ($tableName === null) {
            $sql .= ' tc.relname AS table_name, tn.nspname AS schema_name,';
        }

        $sql .= <<<'SQL'
                   quote_ident(ic.relname) AS relname,
                   i.indisunique,
                   i.indisprimary,
                   i.indkey,
                   i.indrelid,
                   pg_get_expr(indpred, indrelid) AS "where"
              FROM pg_index i
                   JOIN pg_class AS tc ON tc.oid = i.indrelid
                   JOIN pg_namespace tn ON tn.oid = tc.relnamespace
                   JOIN pg_class AS ic ON ic.oid = i.indexrelid
             WHERE ic.oid IN (
                SELECT indexrelid
                FROM pg_index i, pg_class c, pg_namespace n
SQL;

        $conditions = array_merge([
            'c.oid = i.indrelid',
            'c.relnamespace = n.oid',
        ], $this->buildQueryConditions($tableName));

        $sql .= ' WHERE ' . implode(' AND ', $conditions) . ')';

        return $this->_conn->executeQuery($sql);
    }

    protected function selectForeignKeyColumns(string $databaseName, ?string $tableName = null): Result
    {
        $sql = 'SELECT';

        if ($tableName === null) {
            $sql .= ' tc.relname AS table_name, tn.nspname AS schema_name,';
        }

        $sql .= <<<'SQL'
                  quote_ident(r.conname) as conname,
                  pg_get_constraintdef(r.oid, true) as condef
                  FROM pg_constraint r
                      JOIN pg_class AS tc ON tc.oid = r.conrelid
                      JOIN pg_namespace tn ON tn.oid = tc.relnamespace
                  WHERE r.conrelid IN
                  (
                      SELECT c.oid
                      FROM pg_class c, pg_namespace n
SQL;

        $conditions = array_merge(['n.oid = c.relnamespace'], $this->buildQueryConditions($tableName));

        $sql .= ' WHERE ' . implode(' AND ', $conditions) . ") AND r.contype = 'f'";

        return $this->_conn->executeQuery($sql);
    }

    
    protected function fetchTableOptionsByTable(string $databaseName, ?string $tableName = null): array
    {
        $sql = <<<'SQL'
SELECT c.relname,
       obj_description(c.oid, 'pg_class') AS comment
FROM pg_class c
     INNER JOIN pg_namespace n
         ON n.oid = c.relnamespace
SQL;

        $conditions = array_merge(["c.relkind = 'r'"], $this->buildQueryConditions($tableName));

        $sql .= ' WHERE ' . implode(' AND ', $conditions);

        return $this->_conn->fetchAllAssociativeIndexed($sql);
    }

    
    private function buildQueryConditions($tableName): array
    {
        $conditions = [];

        if ($tableName !== null) {
            if (strpos($tableName, '.') !== false) {
                [$schemaName, $tableName] = explode('.', $tableName);
                $conditions[]             = 'n.nspname = ' . $this->_platform->quoteStringLiteral($schemaName);
            } else {
                $conditions[] = 'n.nspname = ANY(current_schemas(false))';
            }

            $identifier   = new Identifier($tableName);
            $conditions[] = 'c.relname = ' . $this->_platform->quoteStringLiteral($identifier->getName());
        }

        $conditions[] = "n.nspname NOT IN ('pg_catalog', 'information_schema', 'pg_toast')";

        return $conditions;
    }
}

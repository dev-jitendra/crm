<?php


namespace Espo\Core\Utils\Database\Dbal\Platforms;

use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager as BasePostgreSQLSchemaManager;

class PostgreSQLSchemaManager extends BasePostgreSQLSchemaManager
{
    
    protected function _getPortableTableIndexesList($tableIndexes, $tableName = null)
    {
        $indexes = parent::_getPortableTableIndexesList($tableIndexes, $tableName);

        foreach ($tableIndexes as $row) {
            $key = $row['relname'];

            if ($key === "idx_{$tableName}_system_full_text_search") {
                $sql = "SELECT indexdef FROM pg_indexes WHERE indexname = '{$key}'";

                $rows = $this->_conn->fetchAllAssociative($sql);

                if (!$rows) {
                    continue;
                }

                $columns = self::parseColumnsIndexFromDeclaration($rows[0]['indexdef']);

                $indexes[$key] = new Index(
                    $key,
                    $columns,
                    false,
                    false,
                    ['fulltext']
                );
            }
        }

        return $indexes;
    }

    
    private static function parseColumnsIndexFromDeclaration(string $string): array
    {
        preg_match('/to_tsvector\((.*),(.*)\)/i', $string, $matches);

        if (!$matches || count($matches) < 3) {
            return [];
        }

        $part = $matches[2];

        $part = str_replace("|| ' '::text", '', $part);
        $part = str_replace("::text", '', $part);
        $part = str_replace(" ", '', $part);
        $part = str_replace("||", ' ', $part);
        $part = str_replace("(", '', $part);
        $part = str_replace(")", '', $part);

        $list = array_map(
            fn ($item) => trim($item),
            explode(' ', $part)
        );

        return $list;
    }
}

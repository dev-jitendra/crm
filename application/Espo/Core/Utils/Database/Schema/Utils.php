<?php


namespace Espo\Core\Utils\Database\Schema;

use Espo\Core\Utils\Util;
use Espo\ORM\Defs\IndexDefs;

class Utils
{
    
    public static function getIndexes(array $defs, array $ignoreFlags = []): array
    {
        $indexList = [];

        foreach ($defs as $entityType => $entityParams) {
            $indexes = $entityParams['indexes'] ?? [];

            foreach ($indexes as $indexName => $indexParams) {
                $indexDefs = IndexDefs::fromRaw($indexParams, $indexName);

                $tableIndexName = $indexParams['key'] ?? null;

                if (!$tableIndexName) {
                    continue;
                }

                $columns = $indexDefs->getColumnList();
                $flags = $indexDefs->getFlagList();

                if ($flags !== []) {
                    $skipIndex = false;

                    foreach ($ignoreFlags as $ignoreFlag) {
                        if (($flagKey = array_search($ignoreFlag, $flags)) !== false) {
                            unset($flags[$flagKey]);

                            $skipIndex = true;
                        }
                    }

                    if ($skipIndex && empty($flags)) {
                        continue;
                    }

                    $indexList[$entityType][$tableIndexName]['flags'] = $flags;
                }

                if ($columns !== []) {
                    $indexType = self::getIndexTypeByIndexDefs($indexDefs);

                    
                    $indexList[$entityType][$tableIndexName]['type'] = $indexType;

                    $indexList[$entityType][$tableIndexName]['columns'] = array_map(
                        fn ($item) => Util::toUnderScore($item),
                        $columns
                    );
                }
            }
        }

        
        return $indexList;
    }

    private static function getIndexTypeByIndexDefs(IndexDefs $indexDefs): string
    {
        if ($indexDefs->isUnique()) {
            return 'unique';
        }

        if (in_array('fulltext', $indexDefs->getFlagList())) {
            return 'fulltext';
        }

        return 'index';
    }

    
    public static function getFieldListExceededIndexMaxLength(
        array $ormMeta,
        $indexMaxLength = 1000,
        array $indexList = null,
        $characterLength = 4
    ) {

        $permittedFieldTypeList = [
            'varchar',
        ];

        $fields = [];

        if (!isset($indexList)) {
            $indexList = self::getIndexes($ormMeta, ['fulltext']);
        }

        foreach ($indexList as $entityName => $indexes) {
            foreach ($indexes as $indexName => $indexParams) {
                $columnList = $indexParams['columns'];

                $indexLength = 0;

                foreach ($columnList as $columnName) {
                    $fieldName = Util::toCamelCase($columnName);

                    if (!isset($ormMeta[$entityName]['fields'][$fieldName])) {
                        continue;
                    }

                    $indexLength += self::getFieldLength(
                        $ormMeta[$entityName]['fields'][$fieldName],
                        $characterLength
                    );
                }

                if ($indexLength > $indexMaxLength) {
                    foreach ($columnList as $columnName) {
                        $fieldName = Util::toCamelCase($columnName);

                        if (!isset($ormMeta[$entityName]['fields'][$fieldName])) {
                            continue;
                        }

                        $fieldType = self::getFieldType($ormMeta[$entityName]['fields'][$fieldName]);

                        if (in_array($fieldType, $permittedFieldTypeList)) {
                            if (!isset($fields[$entityName]) || !in_array($fieldName, $fields[$entityName])) {
                                $fields[$entityName][] = $fieldName;
                            }
                        }
                    }
                }
            }
        }

        return $fields;
    }

    
    private static function getFieldLength(array $ormFieldDefs, $characterLength = 4)
    {
        $length = 0;

        if (isset($ormFieldDefs['notStorable']) && $ormFieldDefs['notStorable']) {
            return $length;
        }

        $defaultLength = [
            'datetime' => 8,
            'time' => 4,
            'int' => 4,
            'bool' => 1,
            'float' => 4,
            'varchar' => 255,
        ];

        $type = self::getDbFieldType($ormFieldDefs);

        $length = $defaultLength[$type] ?? $length;
        

        switch ($type) {
            case 'varchar':
                $length = $length * $characterLength;

                break;
        }

        return $length;
    }

    
    private static function getDbFieldType(array $ormFieldDefs)
    {
        return $ormFieldDefs['dbType'] ?? $ormFieldDefs['type'];
    }

    
    private static function getFieldType(array $ormFieldDefs): string
    {
        return $ormFieldDefs['type'] ?? self::getDbFieldType($ormFieldDefs);
    }
}

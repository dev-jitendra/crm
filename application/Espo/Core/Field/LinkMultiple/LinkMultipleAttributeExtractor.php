<?php


namespace Espo\Core\Field\LinkMultiple;

use Espo\ORM\Value\AttributeExtractor;

use Espo\Core\Field\LinkMultiple;

use stdClass;
use InvalidArgumentException;


class LinkMultipleAttributeExtractor implements AttributeExtractor
{
    
    public function extract(object $value, string $field): stdClass
    {
        if (!$value instanceof LinkMultiple) {
            throw new InvalidArgumentException();
        }

        $nameMap = (object) [];
        $columnData = (object) [];

        foreach ($value->getList() as $item) {
            $id = $item->getId();

            $nameMap->$id = $item->getName();

            $columnItemData = (object) [];

            foreach ($item->getColumnList() as $column) {
                $columnItemData->$column = $item->getColumnValue($column);
            }

            $columnData->$id = $columnItemData;
        }

        return (object) [
            $field . 'Ids' => $value->getIdList(),
            $field . 'Names' => $nameMap,
            $field . 'Columns' => $columnData,
        ];
    }

    public function extractFromNull(string $field): stdClass
    {
        return (object) [
            $field . 'Ids' => [],
            $field . 'Names' => (object) [],
            $field . 'Columns' => (object) [],
        ];
    }
}

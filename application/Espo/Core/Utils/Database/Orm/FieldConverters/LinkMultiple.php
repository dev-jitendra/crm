<?php


namespace Espo\Core\Utils\Database\Orm\FieldConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\FieldConverter;
use Espo\ORM\Defs\FieldDefs;
use Espo\ORM\Type\AttributeType;

class LinkMultiple implements FieldConverter
{
    public function convert(FieldDefs $fieldDefs, string $entityType): EntityDefs
    {
        $name = $fieldDefs->getName();

        $idsName = $name . 'Ids';
        $namesName = $name . 'Names';
        $columnsName = $name . 'Columns';

        $idsDefs = AttributeDefs::create($idsName)
            ->withType(AttributeType::JSON_ARRAY)
            ->withNotStorable()
            ->withParamsMerged([
                'isLinkMultipleIdList' => true,
                'relation' => $name,
                'isUnordered' => true,
                'attributeRole' => 'idList',
                'fieldType' => 'linkMultiple',
            ]);

        
        $defaults = $fieldDefs->getParam('defaultAttributes') ?? [];

        if (array_key_exists($idsName, $defaults)) {
            $idsDefs = $idsDefs->withDefault($defaults[$idsName]);
        }

        $namesDefs = AttributeDefs::create($namesName)
            ->withType(AttributeType::JSON_OBJECT)
            ->withNotStorable()
            ->withParamsMerged([
                'isLinkMultipleNameMap' => true,
                'attributeRole' => 'nameMap',
                'fieldType' => 'linkMultiple',
            ]);

        $orderBy = $fieldDefs->getParam('orderBy');
        $orderDirection = $fieldDefs->getParam('orderDirection');

        if ($orderBy) {
            $idsDefs = $idsDefs->withParam('orderBy', $orderBy);

            if ($orderDirection !== null) {
                $idsDefs = $idsDefs->withParam('orderDirection', $orderDirection);
            }
        }

        $columns = $fieldDefs->getParam('columns');

        $columnsDefs = $columns ?
            AttributeDefs::create($columnsName)
                ->withType(AttributeType::JSON_OBJECT)
                ->withNotStorable()
                ->withParamsMerged([
                    'columns' => $columns,
                    'attributeRole' => 'columnsMap',
                ])
            : null;

        $entityDefs = EntityDefs::create()
            ->withAttribute($idsDefs)
            ->withAttribute($namesDefs);

        if ($columnsDefs) {
            $entityDefs = $entityDefs->withAttribute($columnsDefs);
        }

        return $entityDefs;
    }
}

<?php


namespace Espo\Core\Utils\Database\Orm\FieldConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\FieldConverter;
use Espo\ORM\Defs\FieldDefs;
use Espo\ORM\Type\AttributeType;

class LinkParent implements FieldConverter
{
    private const TYPE_LENGTH = 100;

    public function convert(FieldDefs $fieldDefs, string $entityType): EntityDefs
    {
        $name = $fieldDefs->getName();

        $idName = $name . 'Id';
        $typeName = $name . 'Type';
        $nameName = $name . 'Name';

        $idDefs = AttributeDefs::create($idName)
            ->withType(AttributeType::FOREIGN_ID)
            ->withParamsMerged([
                'index' => $name,
                'attributeRole' => 'id',
                'fieldType' => 'linkParent',
            ]);

        $typeDefs = AttributeDefs::create($typeName)
            ->withType(AttributeType::FOREIGN_TYPE)
            ->withParam('notNull', false)
            ->withParam('index', $name)
            ->withLength(self::TYPE_LENGTH)
            ->withParamsMerged([
                'attributeRole' => 'type',
                'fieldType' => 'linkParent',
            ]);

        $nameDefs = AttributeDefs::create($nameName)
            ->withType(AttributeType::VARCHAR)
            ->withNotStorable()
            ->withParamsMerged([
                'relation' => $name,
                'isParentName' => true,
                'attributeRole' => 'name',
                'fieldType' => 'linkParent',
            ]);

        if ($fieldDefs->isNotStorable()) {
            $idDefs = $idDefs->withNotStorable();
            $typeDefs = $typeDefs->withNotStorable();
        }

        
        $defaults = $fieldDefs->getParam('defaultAttributes') ?? [];

        if (array_key_exists($idName, $defaults)) {
            $idDefs = $idDefs->withDefault($defaults[$idName]);
        }

        if (array_key_exists($typeName, $defaults)) {
            $typeDefs = $idDefs->withDefault($defaults[$typeName]);
        }

        return EntityDefs::create()
            ->withAttribute($idDefs)
            ->withAttribute($typeDefs)
            ->withAttribute($nameDefs);
    }
}

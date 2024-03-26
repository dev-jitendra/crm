<?php


namespace Espo\Core\Utils\Database\Orm\FieldConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\FieldConverter;
use Espo\ORM\Defs\FieldDefs;
use Espo\ORM\Type\AttributeType;

class Link implements FieldConverter
{
    public function convert(FieldDefs $fieldDefs, string $entityType): EntityDefs
    {
        $name = $fieldDefs->getName();

        $idName = $name . 'Id';
        $nameName = $name . 'Name';

        $idDefs = AttributeDefs::create($idName)
            ->withType(AttributeType::FOREIGN_ID)
            ->withParamsMerged([
                'index' => $name,
                'attributeRole' => 'id',
                'fieldType' => 'link',
            ]);

        $nameDefs = AttributeDefs::create($nameName)
            ->withType(AttributeType::VARCHAR)
            ->withNotStorable()
            ->withParamsMerged([
                'attributeRole' => 'name',
                'fieldType' => 'link',
            ]);

        if ($fieldDefs->isNotStorable()) {
            $idDefs = $idDefs->withNotStorable();
        }

        
        $defaults = $fieldDefs->getParam('defaultAttributes') ?? [];

        if (array_key_exists($idName, $defaults)) {
            $idDefs = $idDefs->withDefault($defaults[$idName]);
        }

        return EntityDefs::create()
            ->withAttribute($idDefs)
            ->withAttribute($nameDefs);
    }
}

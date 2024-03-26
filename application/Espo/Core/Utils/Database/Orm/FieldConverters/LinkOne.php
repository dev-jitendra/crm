<?php


namespace Espo\Core\Utils\Database\Orm\FieldConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\FieldConverter;
use Espo\ORM\Defs\FieldDefs;
use Espo\ORM\Type\AttributeType;

class LinkOne implements FieldConverter
{
    public function convert(FieldDefs $fieldDefs, string $entityType): EntityDefs
    {
        $name = $fieldDefs->getName();

        return EntityDefs::create()
            ->withAttribute(
                AttributeDefs::create($name . 'Id')
                    ->withType(AttributeType::VARCHAR)
                    ->withNotStorable()
                    ->withParamsMerged([
                        'attributeRole' => 'id',
                        'fieldType' => 'linkOne',
                    ])
            )
            ->withAttribute(
                AttributeDefs::create($name . 'Name')
                    ->withType(AttributeType::VARCHAR)
                    ->withNotStorable()
                    ->withParamsMerged([
                        'attributeRole' => 'name',
                        'fieldType' => 'linkOne',
                    ])
            );
    }
}

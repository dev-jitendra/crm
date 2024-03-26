<?php


namespace Espo\Core\Utils\Database\Orm\FieldConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\FieldConverter;
use Espo\ORM\Defs\FieldDefs;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Type\AttributeType;

class AttachmentMultiple implements FieldConverter
{
    public function convert(FieldDefs $fieldDefs, string $entityType): EntityDefs
    {
        $name = $fieldDefs->getName();

        return EntityDefs::create()
            ->withAttribute(
                AttributeDefs::create($name . 'Ids')
                    ->withType(AttributeType::JSON_ARRAY)
                    ->withNotStorable()
                    ->withParamsMerged([
                        'orderBy' => [
                            ['createdAt', Order::ASC],
                            ['name', Order::ASC],
                        ],
                        'isLinkMultipleIdList' => true,
                        'relation' => $name,
                    ])
            )
            ->withAttribute(
                AttributeDefs::create($name . 'Names')
                    ->withType(AttributeType::JSON_OBJECT)
                    ->withNotStorable()
                    ->withParamsMerged([
                        'isLinkMultipleNameMap' => true,
                    ])
            );
    }
}

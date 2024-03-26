<?php


namespace Espo\Core\Utils\Database\Orm\LinkConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\Defs\RelationDefs;
use Espo\Core\Utils\Database\Orm\LinkConverter;
use Espo\Core\Utils\Util;
use Espo\ORM\Defs\RelationDefs as LinkDefs;
use Espo\ORM\Type\AttributeType;
use Espo\ORM\Type\RelationType;

class ManyMany implements LinkConverter
{
    public function convert(LinkDefs $linkDefs, string $entityType): EntityDefs
    {
        $name = $linkDefs->getName();
        $foreignEntityType = $linkDefs->getForeignEntityType();
        $foreignRelationName = $linkDefs->getForeignRelationName();
        $hasField = $linkDefs->getParam('hasField');
        $columnAttributeMap = $linkDefs->getParam('columnAttributeMap');

        $relationshipName = $linkDefs->hasRelationshipName() ?
            $linkDefs->getRelationshipName() :
            self::composeRelationshipName($entityType, $foreignEntityType);

        $key1 = lcfirst($entityType) . 'Id';
        $key2 = lcfirst($foreignEntityType) . 'Id';

        if ($key1 === $key2) {
            [$key1, $key2] = strcmp($name, $foreignRelationName) ?
                ['leftId', 'rightId'] :
                ['rightId', 'leftId'];
        }

        $relationDefs = RelationDefs::create($name)
            ->withType(RelationType::MANY_MANY)
            ->withForeignEntityType($foreignEntityType)
            ->withRelationshipName($relationshipName)
            ->withKey('id')
            ->withForeignKey('id')
            ->withMidKeys($key1, $key2)
            ->withForeignRelationName($foreignRelationName);

        if ($columnAttributeMap) {
            $relationDefs = $relationDefs->withParam('columnAttributeMap', $columnAttributeMap);
        }

        return EntityDefs::create()
            ->withAttribute(
                AttributeDefs::create($name . 'Ids')
                    ->withType(AttributeType::JSON_ARRAY)
                    ->withNotStorable()
                    ->withParam('isLinkStub', !$hasField) 
            )
            ->withAttribute(
                AttributeDefs::create($name . 'Names')
                    ->withType(AttributeType::JSON_OBJECT)
                    ->withNotStorable()
                    ->withParam('isLinkStub', !$hasField) 
            )
            ->withRelation($relationDefs);
    }

    private static function composeRelationshipName(string $left, string $right): string
    {
        $parts = [
            Util::toCamelCase(lcfirst($left)),
            Util::toCamelCase(lcfirst($right)),
        ];

        sort($parts);

        return Util::toCamelCase(implode('_', $parts));
    }
}

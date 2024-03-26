<?php


namespace Espo\Core\Utils\Database\Orm\LinkConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\Defs\RelationDefs;
use Espo\Core\Utils\Database\Orm\LinkConverter;
use Espo\ORM\Defs\RelationDefs as LinkDefs;
use Espo\ORM\Type\AttributeType;
use Espo\ORM\Type\RelationType;

class BelongsToParent implements LinkConverter
{
    private const TYPE_LENGTH = 100;

    public function convert(LinkDefs $linkDefs, string $entityType): EntityDefs
    {
        $name = $linkDefs->getName();

        $foreignRelationName = $linkDefs->hasForeignRelationName() ?
            $linkDefs->getForeignRelationName() : null;

        $idName = $name . 'Id';
        $nameName = $name . 'Name';
        $typeName = $name . 'Type';

        $relationDefs = RelationDefs::create($name)
            ->withType(RelationType::BELONGS_TO_PARENT)
            ->withKey($idName)
            ->withForeignRelationName($foreignRelationName);

        return EntityDefs::create()
            ->withAttribute(
                AttributeDefs::create($idName)
                    ->withType(AttributeType::FOREIGN_ID)
                    ->withParam('index', $name)
            )
            ->withAttribute(
                AttributeDefs::create($typeName)
                    ->withType(AttributeType::FOREIGN_TYPE)
                    ->withParam('notNull', false) 
                    ->withParam('index', $name)
                    ->withLength(self::TYPE_LENGTH)
            )
            ->withAttribute(
                AttributeDefs::create($nameName)
                    ->withType(AttributeType::VARCHAR)
                    ->withNotStorable()
            )
            ->withRelation($relationDefs);
    }
}

<?php


namespace Espo\Core\Utils\Database\Orm\LinkConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\Defs\RelationDefs;
use Espo\Core\Utils\Database\Orm\LinkConverter;
use Espo\ORM\Defs\RelationDefs as LinkDefs;
use Espo\ORM\Type\AttributeType;
use Espo\ORM\Type\RelationType;

class BelongsTo implements LinkConverter
{
    public function convert(LinkDefs $linkDefs, string $entityType): EntityDefs
    {
        $name = $linkDefs->getName();
        $foreignEntityType = $linkDefs->getForeignEntityType();
        $foreignRelationName = $linkDefs->hasForeignRelationName() ? $linkDefs->getForeignRelationName() : null;
        $noIndex = $linkDefs->getParam('noIndex');
        $noForeignName = $linkDefs->getParam('noForeignName');
        $foreignName = $linkDefs->getParam('foreignName') ?? 'name';
        $noJoin = $linkDefs->getParam('noJoin');

        $idName = $name . 'Id';
        $nameName = $name . 'Name';

        $idAttributeDefs = AttributeDefs::create($idName)
            ->withType(AttributeType::FOREIGN_ID)
            ->withParam('index', !$noIndex);

        $relationDefs = RelationDefs::create($name)
            ->withType(RelationType::BELONGS_TO)
            ->withForeignEntityType($foreignEntityType)
            ->withKey($idName)
            ->withForeignKey('id')
            ->withForeignRelationName($foreignRelationName);

        $nameAttributeDefs = !$noForeignName ?
            (
                $noJoin ?
                    AttributeDefs::create($nameName)
                        ->withType(AttributeType::VARCHAR)
                        ->withNotStorable()
                        ->withParam('relation', $name)
                        ->withParam('foreign', $foreignName) :
                    AttributeDefs::create($nameName)
                        ->withType(AttributeType::FOREIGN)
                        ->withNotStorable(true) 
                        ->withParam('relation', $name)
                        ->withParam('foreign', $foreignName)
            ) : null;

        $entityDefs = EntityDefs::create()
            ->withAttribute($idAttributeDefs)
            ->withRelation($relationDefs);

        if ($nameAttributeDefs) {
            $entityDefs = $entityDefs->withAttribute($nameAttributeDefs);
        }

        return $entityDefs;
    }
}

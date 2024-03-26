<?php


namespace Espo\Core\Utils\Database\Orm\LinkConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\Defs\RelationDefs;
use Espo\Core\Utils\Database\Orm\LinkConverter;
use Espo\ORM\Defs\RelationDefs as LinkDefs;
use Espo\ORM\Type\AttributeType;
use Espo\ORM\Type\RelationType;

class HasOne implements LinkConverter
{
    public function convert(LinkDefs $linkDefs, string $entityType): EntityDefs
    {
        $name = $linkDefs->getName();
        $foreignEntityType = $linkDefs->getForeignEntityType();
        $foreignRelationName = $linkDefs->hasForeignRelationName() ? $linkDefs->getForeignRelationName() : null;
        $noForeignName = $linkDefs->getParam('noForeignName');
        $foreignName = $linkDefs->getParam('foreignName') ?? 'name';
        $noJoin = $linkDefs->getParam('noJoin');

        $idName = $name . 'Id';
        $nameName = $name . 'Name';

        $idAttributeDefs = AttributeDefs::create($idName)
            ->withType($noJoin ? AttributeType::VARCHAR : AttributeType::FOREIGN)
            ->withNotStorable()
            ->withParam('relation', $name)
            ->withParam('foreign', 'id');

        $nameAttributeDefs = !$noForeignName ?
            (
            AttributeDefs::create($nameName)
                ->withType($noJoin ? AttributeType::VARCHAR : AttributeType::FOREIGN)
                ->withNotStorable()
                ->withParam('relation', $name)
                ->withParam('foreign', $foreignName)
            ) : null;

        $relationDefs = RelationDefs::create($name)
            ->withType(RelationType::HAS_ONE)
            ->withForeignEntityType($foreignEntityType);

        if ($foreignRelationName) {
            $relationDefs = $relationDefs
                ->withForeignKey($foreignRelationName . 'Id')
                ->withForeignRelationName($foreignRelationName);
        }

        $entityDefs = EntityDefs::create()
            ->withAttribute($idAttributeDefs)
            ->withRelation($relationDefs);

        if ($nameAttributeDefs) {
            $entityDefs = $entityDefs->withAttribute($nameAttributeDefs);
        }

        return $entityDefs;
    }
}

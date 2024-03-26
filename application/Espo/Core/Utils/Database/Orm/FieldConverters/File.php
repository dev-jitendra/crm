<?php


namespace Espo\Core\Utils\Database\Orm\FieldConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\Defs\RelationDefs;
use Espo\Core\Utils\Database\Orm\FieldConverter;
use Espo\Entities\Attachment;
use Espo\ORM\Defs\FieldDefs;
use Espo\ORM\Type\AttributeType;
use Espo\ORM\Type\RelationType;

class File implements FieldConverter
{
    public function convert(FieldDefs $fieldDefs, string $entityType): EntityDefs
    {
        $name = $fieldDefs->getName();

        $idName = $name . 'Id';
        $nameName = $name . 'Name';

        $idDefs = AttributeDefs::create($idName)
            ->withType(AttributeType::FOREIGN_ID)
            ->withParam('index', false);

        $nameDefs = AttributeDefs::create($nameName)
            ->withType(AttributeType::FOREIGN);

        if ($fieldDefs->isNotStorable()) {
            $idDefs = $idDefs->withNotStorable();

            $nameDefs = $nameDefs->withType(AttributeType::VARCHAR);
        }

        
        $defaults = $fieldDefs->getParam('defaultAttributes') ?? [];

        if (array_key_exists($idName, $defaults)) {
            $idDefs = $idDefs->withDefault($defaults[$idName]);
        }

        $relationDefs = null;

        if (!$fieldDefs->isNotStorable()) {
            $nameDefs = $nameDefs->withParamsMerged([
                'relation' => $name,
                'foreign' => 'name',
            ]);

            $relationDefs = RelationDefs::create($name)
                ->withType(RelationType::BELONGS_TO)
                ->withForeignEntityType(Attachment::ENTITY_TYPE)
                ->withKey($idName)
                ->withForeignKey('id')
                ->withParam('foreign', null);
        }

        $entityDefs = EntityDefs::create()
            ->withAttribute($idDefs)
            ->withAttribute($nameDefs);

        if ($relationDefs) {
            $entityDefs = $entityDefs->withRelation($relationDefs);
        }

        return $entityDefs;
    }
}

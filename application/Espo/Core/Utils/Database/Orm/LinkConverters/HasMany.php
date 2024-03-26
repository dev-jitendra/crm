<?php


namespace Espo\Core\Utils\Database\Orm\LinkConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\Defs\RelationDefs;
use Espo\Core\Utils\Database\Orm\LinkConverter;
use Espo\Core\Utils\Log;
use Espo\ORM\Defs\RelationDefs as LinkDefs;
use Espo\ORM\Type\AttributeType;
use Espo\ORM\Type\RelationType;

class HasMany implements LinkConverter
{
    public function __construct(private Log $log) {}

    public function convert(LinkDefs $linkDefs, string $entityType): EntityDefs
    {
        if (!$linkDefs->hasForeignRelationName() && $linkDefs->getParam('disabled')) {
            
            
            return EntityDefs::create();
        }

        $name = $linkDefs->getName();
        $foreignEntityType = $linkDefs->getForeignEntityType();
        $foreignRelationName = $linkDefs->getForeignRelationName();
        $hasField = $linkDefs->getParam('hasField');

        $type = RelationType::HAS_MANY;

        

        if ($linkDefs->hasRelationshipName()) {
            $this->log->warning(
                "Issue with the link '{$name}' in '{$entityType}' entity type. Might be the foreign link " .
                "'{$foreignRelationName}' in '{$foreignEntityType}' entity type is missing. " .
                "Remove the problem link manually.");

            return EntityDefs::create();
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
            ->withRelation(
                RelationDefs::create($name)
                    ->withType($type)
                    ->withForeignEntityType($foreignEntityType)
                    ->withForeignKey($foreignRelationName . 'Id')
                    ->withForeignRelationName($foreignRelationName)
            );
    }
}

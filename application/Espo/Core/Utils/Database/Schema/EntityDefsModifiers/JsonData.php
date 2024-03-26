<?php


namespace Espo\Core\Utils\Database\Schema\EntityDefsModifiers;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Schema\EntityDefsModifier;
use Espo\ORM\Defs\EntityDefs as OrmEntityDefs;
use Espo\ORM\Type\AttributeType;


class JsonData implements EntityDefsModifier
{
    public function modify(OrmEntityDefs $entityDefs): EntityDefs
    {
        $sourceIdAttribute = $entityDefs->getAttribute('id');

        $idAttribute = AttributeDefs::create('id')
            ->withType(AttributeType::ID);

        $length = $sourceIdAttribute->getLength();
        $dbType = $sourceIdAttribute->getParam('dbType');

        if ($length) {
            $idAttribute = $idAttribute->withLength($length);
        }

        if ($dbType) {
            $idAttribute = $idAttribute->withDbType($dbType);
        }

        return EntityDefs::create()
            ->withAttribute($idAttribute)
            ->withAttribute(
                AttributeDefs::create('data')
                    ->withType(AttributeType::JSON_OBJECT)
            );
    }
}

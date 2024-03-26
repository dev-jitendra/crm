<?php


namespace Espo\Classes\DuplicateWhereBuilders;

use Espo\Core\Duplicate\WhereBuilder;

use Espo\ORM\Entity;
use Espo\ORM\Query\Part\Condition as Cond;
use Espo\ORM\Query\Part\WhereItem;


class Name implements WhereBuilder
{
    public function build(Entity $entity): ?WhereItem
    {
        if ($entity->get('name')) {
            return Cond::equal(
                Cond::column('name'),
                $entity->get('name')
            );
        }

        return null;
    }
}

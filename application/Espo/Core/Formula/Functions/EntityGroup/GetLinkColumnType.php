<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

use Espo\Core\Exceptions\Error;

use Espo\ORM\EntityManager;

use Espo\Core\Di;

class GetLinkColumnType extends \Espo\Core\Formula\Functions\Base implements
    Di\EntityManagerAware
{
    use Di\EntityManagerSetter;

    
    protected $entityManager;

    
    public function process(\stdClass $item)
    {
        $args = $item->value ?? [];

        if (count($args) < 3) {
            throw new Error("Formula: entity\\isRelated: no argument.");
        }

        $link = $this->evaluate($args[0]);
        $id = $this->evaluate($args[1]);
        $column = $this->evaluate($args[2]);

        $entityType = $this->getEntity()->getEntityType();
        $repository = $this->entityManager->getRDBRepository($entityType);

        return $repository
            ->getRelation($this->getEntity(), $link)
            ->getColumnById($id, $column);
    }
}

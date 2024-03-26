<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

use Espo\Core\Exceptions\Error;

use Espo\ORM\EntityManager;

use Espo\Core\Di;

class IsRelatedType extends \Espo\Core\Formula\Functions\Base implements
    Di\EntityManagerAware
{
    use Di\EntityManagerSetter;

    
    protected $entityManager;

    
    public function process(\stdClass $item)
    {
        if (count($item->value) < 2) {
            throw new Error("isRelated: roo few arguments.");
        }

        $link = $this->evaluate($item->value[0]);
        $id = $this->evaluate($item->value[1]);

        return $this->entityManager
            ->getRDBRepository($this->getEntity()->getEntityType())
            ->getRelation($this->getEntity(), $link)
            ->isRelatedById($id);
    }
}

<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

use Espo\Core\Exceptions\Error;

use Espo\ORM\EntityManager;

use Espo\Core\Di;

use stdClass;

class CountRelatedType extends \Espo\Core\Formula\Functions\Base implements
    Di\EntityManagerAware,
    Di\SelectBuilderFactoryAware
{
    use Di\EntityManagerSetter;
    use Di\SelectBuilderFactorySetter;

    
    protected $entityManager;

    
    public function process(stdClass $item)
    {
        if (count($item->value) < 1) {
            throw new Error("countRelated: roo few arguments.");
        }

        $link = $this->evaluate($item->value[0]);

        if (empty($link)) {
            throw new Error("countRelated: no link passed.");
        }

        $filter = null;

        if (count($item->value) > 1) {
            $filter = $this->evaluate($item->value[1]);
        }

        $entity = $this->getEntity();

        $entityManager = $this->entityManager;

        $foreignEntityType = $entity->getRelationParam($link, 'entity');

        if (empty($foreignEntityType)) {
            throw new Error();
        }

        $builder = $this->selectBuilderFactory
            ->create()
            ->from($foreignEntityType);

        if ($filter) {
              $builder->withPrimaryFilter($filter);
        }

        return $entityManager->getRDBRepository($entity->getEntityType())
            ->getRelation($entity, $link)
            ->clone($builder->build())
            ->count();
    }
}

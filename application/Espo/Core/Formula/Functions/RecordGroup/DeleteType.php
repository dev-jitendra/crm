<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\Core\Formula\EvaluatedArgumentList;
use Espo\Core\Formula\Exceptions\BadArgumentType;
use Espo\Core\Formula\Exceptions\TooFewArguments;
use Espo\Core\Formula\Func;
use Espo\ORM\EntityManager;

class DeleteType implements Func
{
    public function __construct(private EntityManager $entityManager) {}

    public function process(EvaluatedArgumentList $arguments): mixed
    {
        if (count($arguments) < 2) {
            throw TooFewArguments::create(2);
        }

        $entityType = $arguments[0];
        $id = $arguments[1];

        if (!is_string($entityType)) {
            throw BadArgumentType::create(1, 'string');
        }

        if (!is_string($id)) {
            throw BadArgumentType::create(2, 'string');
        }

        $entity = $this->entityManager->getEntityById($entityType, $id);

        if (!$entity) {
            return null;
        }

        $this->entityManager->removeEntity($entity);

        return null;
    }
}

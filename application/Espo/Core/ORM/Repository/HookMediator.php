<?php


namespace Espo\Core\ORM\Repository;

use Espo\ORM\Entity;
use Espo\ORM\Query\Select;
use Espo\ORM\Repository\EmptyHookMediator;
use Espo\Core\HookManager;
use Espo\Core\ORM\Repository\Option\SaveOption;

class HookMediator extends EmptyHookMediator
{
    public function __construct(protected HookManager $hookManager)
    {}

    
    public function afterRelate(
        Entity $entity,
        string $relationName,
        Entity $foreignEntity,
        ?array $columnData,
        array $options
    ): void {

        if (!empty($options[SaveOption::SKIP_HOOKS])) {
            return;
        }

        $hookData = [
            'relationName' => $relationName,
            'relationData' => $columnData,
            'foreignEntity' => $foreignEntity,
            'foreignId' => $foreignEntity->getId(),
        ];

        $this->hookManager->process(
            $entity->getEntityType(),
            'afterRelate',
            $entity,
            $options,
            $hookData
        );
    }

    
    public function afterUnrelate(Entity $entity, string $relationName, Entity $foreignEntity, array $options): void
    {
        if (!empty($options[Option\SaveOption::SKIP_HOOKS])) {
            return;
        }

        $hookData = [
            'relationName' => $relationName,
            'foreignEntity' => $foreignEntity,
            'foreignId' => $foreignEntity->getId(),
        ];

        $this->hookManager->process(
            $entity->getEntityType(),
            'afterUnrelate',
            $entity,
            $options,
            $hookData
        );
    }

    
    public function afterMassRelate(Entity $entity, string $relationName, Select $query, array $options): void
    {
        if (!empty($options[SaveOption::SKIP_HOOKS])) {
            return;
        }

        $hookData = [
            'relationName' => $relationName,
            'query' => $query,
        ];

        $this->hookManager->process(
            $entity->getEntityType(),
            'afterMassRelate',
            $entity,
            $options,
            $hookData
        );
    }
}

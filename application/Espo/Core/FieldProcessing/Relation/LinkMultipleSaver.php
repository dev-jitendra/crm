<?php


namespace Espo\Core\FieldProcessing\Relation;

use Espo\ORM\Entity;

use Espo\Core\FieldProcessing\Saver\Params;
use Espo\Core\ORM\EntityManager;
use Espo\Core\ORM\Repository\Option\SaveOption;

class LinkMultipleSaver
{
    public function __construct(private EntityManager $entityManager)
    {}

    public function process(Entity $entity, string $name, Params $params): void
    {
        $entityType = $entity->getEntityType();

        $repository = $this->entityManager->getRDBRepository($entityType);

        $idListAttribute = $name . 'Ids';
        $columnsAttribute = $name . 'Columns';

        $defs = $this->entityManager->getDefs()->getEntity($entityType);

        $skipCreate = $params->getOption('skipLinkMultipleCreate') ?? false;
        $skipRemove = $params->getOption('skipLinkMultipleRemove') ?? false;
        $skipUpdate = $params->getOption('skipLinkMultipleUpdate') ?? false;
        $skipHooks = $params->getOption('skipLinkMultipleHooks') ?? false;

        if ($entity->isNew()) {
            $skipRemove = true;
            $skipUpdate = true;
        }

        if ($entity->has($idListAttribute)) {
            $specifiedIdList = $entity->get($idListAttribute);
        }
        else if ($entity->has($columnsAttribute)) {
            $skipRemove = true;

            $specifiedIdList = array_keys(
                get_object_vars(
                    $entity->get($columnsAttribute) ?? (object) []
                )
            );
        }
        else {
            return;
        }

        if (!is_array($specifiedIdList)) {
            return;
        }

        $toRemoveIdList = [];
        $existingIdList = [];
        $toUpdateIdList = [];
        $toCreateIdList = [];

        $existingColumnsData = (object) [];

        $columns = null;

        if ($defs->hasField($name)) {
            $columns = $defs->getField($name)->getParam('columns');
        }

        $columnData = !empty($columns) ?
            $entity->get($columnsAttribute) :
            null;

        if (!$skipRemove || !$skipUpdate) {
            $foreignEntityList = $repository->getRelation($entity, $name)->find();

            foreach ($foreignEntityList as $foreignEntity) {
                $existingIdList[] = $foreignEntity->getId();

                if (empty($columns)) {
                    continue;
                }

                $data = (object) [];

                $foreignId = $foreignEntity->getId();

                foreach ($columns as $columnName => $columnField) {
                    $data->$columnName = $foreignEntity->get($columnField);
                }

                $existingColumnsData->$foreignId = $data;

                if (!$entity->isNew()) {
                    $entity->setFetched($columnsAttribute, $existingColumnsData);
                }
            }
        }

        if (!$entity->isNew()) {
            if ($entity->has($idListAttribute) && !$entity->hasFetched($idListAttribute)) {
                $entity->setFetched($idListAttribute, $existingIdList);
            }

            if ($entity->has($columnsAttribute) && !empty($columns)) {
                $entity->setFetched($columnsAttribute, $existingColumnsData);
            }
        }

        foreach ($existingIdList as $id) {
            if (!in_array($id, $specifiedIdList)) {
                if (!$skipRemove) {
                    $toRemoveIdList[] = $id;
                }

                continue;
            }

            if ($skipUpdate || empty($columns)) {
                continue;
            }

            foreach ($columns as $columnName => $columnField) {
                if (!isset($columnData->$id) || !is_object($columnData->$id)) {
                    continue;
                }

                if (
                    property_exists($columnData->$id, $columnName) &&
                    (
                        !property_exists($existingColumnsData->$id, $columnName) ||
                        $columnData->$id->$columnName !== $existingColumnsData->$id->$columnName
                    )
                ) {
                    $toUpdateIdList[] = $id;
                }
            }
        }

        if (!$skipCreate) {
            foreach ($specifiedIdList as $id) {
                if (!in_array($id, $existingIdList)) {
                    $toCreateIdList[] = $id;
                }
            }
        }

        foreach ($toCreateIdList as $id) {
            $data = null;

            if (!empty($columns) && isset($columnData->$id)) {
                $data = (array) $columnData->$id;
            }

            $repository->getRelation($entity, $name)->relateById($id, $data, [
                SaveOption::SKIP_HOOKS => $skipHooks,
            ]);
        }

        foreach ($toRemoveIdList as $id) {
            $repository->getRelation($entity, $name)->unrelateById($id, [
                SaveOption::SKIP_HOOKS => $skipHooks,
            ]);
        }

        foreach ($toUpdateIdList as $id) {
            $data = (array) $columnData->$id;

            $repository->getRelation($entity, $name)->updateColumnsById($id, (array) $data);
        }
    }
}

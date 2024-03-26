<?php


namespace Espo\Core\Repositories;

use Espo\ORM\Entity;
use Espo\ORM\Mapper\BaseMapper;


class CategoryTree extends Database
{
    
    protected function afterSave(Entity $entity, array $options = [])
    {
        parent::afterSave($entity, $options);

        $parentId = $entity->get('parentId');

        $em = $this->entityManager;

        $pathEntityType = $entity->getEntityType() . 'Path';

        if ($entity->isNew()) {
            if ($parentId) {
                $subSelect1 = $em->getQueryBuilder()
                    ->select()
                    ->from($pathEntityType)
                    ->select(['ascendorId', "'" . $entity->getId() . "'"])
                    ->where([
                        'descendorId' => $parentId,
                    ])
                    ->build();

                $subSelect2 = $em->getQueryBuilder()
                    ->select()
                    ->select(["'" . $entity->getId() . "'", "'" . $entity->getId() . "'"])
                    ->build();

                $select = $em->getQueryBuilder()
                    ->union()
                    ->all()
                    ->query($subSelect1)
                    ->query($subSelect2)
                    ->build();

                $insert = $em->getQueryBuilder()
                    ->insert()
                    ->into($pathEntityType)
                    ->columns(['ascendorId', 'descendorId'])
                    ->valuesQuery($select)
                    ->build();

                $em->getQueryExecutor()->execute($insert);

                return;
            }

            $insert = $em->getQueryBuilder()
                ->insert()
                ->into($pathEntityType)
                ->columns(['ascendorId', 'descendorId'])
                ->values([
                    'ascendorId' => $entity->getId(),
                    'descendorId' => $entity->getId(),
                ])
                ->build();

            $em->getQueryExecutor()->execute($insert);

            return;
        }

        if (!$entity->isAttributeChanged('parentId')) {
            return;
        }

        $delete = $em->getQueryBuilder()
            ->delete()
            ->from($pathEntityType, 'a')
            ->join(
                $pathEntityType,
                'd',
                [
                    'd.descendorId:' => 'a.descendorId',
                ]
            )
            ->leftJoin(
                $pathEntityType,
                'x',
                [
                    'x.ascendorId:' => 'd.descendorId',
                    'x.descendorId:' => 'a.ascendorId',
                ]
            )
            ->where([
                'd.descendorId' => $entity->getId(),
                'x.ascendorId' => null,
            ])
            ->build();

        $em->getQueryExecutor()->execute($delete);

        if (!empty($parentId)) {
            $select = $em->getQueryBuilder()
                ->select()
                ->from($pathEntityType)
                ->select(['ascendorId', 's.descendorId'])
                ->join($pathEntityType, 's')
                ->where([
                    's.ascendorId' => $entity->getId(),
                    'descendorId' => $parentId,
                ])
                ->build();

            $insert = $em->getQueryBuilder()
                ->insert()
                ->into($pathEntityType)
                ->columns(['ascendorId', 'descendorId'])
                ->valuesQuery($select)
                ->build();

            $em->getQueryExecutor()->execute($insert);
        }
    }

    protected function afterRemove(Entity $entity, array $options = [])
    {
        parent::afterRemove($entity, $options);

        $pathEntityType = $entity->getEntityType() . 'Path';

        $em = $this->entityManager;

        $delete = $em->getQueryBuilder()
            ->delete()
            ->from($pathEntityType)
            ->where([
                'descendorId' => $entity->getId(),
            ])
            ->build();

        $em->getQueryExecutor()->execute($delete);

        $mapper = $em->getMapper();

        if (!$mapper instanceof BaseMapper) {
            return;
        }

        $mapper->deleteFromDb($entity->getEntityType(), $entity->getId());
    }
}

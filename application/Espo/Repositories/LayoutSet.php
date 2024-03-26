<?php


namespace Espo\Repositories;

use Espo\Core\Repositories\Database;
use Espo\Entities\LayoutRecord;
use Espo\Entities\LayoutSet as LayoutSetEntity;
use Espo\ORM\Entity;


class LayoutSet extends Database
{
    protected function afterSave(Entity $entity, array $options = [])
    {
        parent::afterSave($entity);

        if (!$entity->isNew() && $entity->has('layoutList')) {
            $listBefore = $entity->getFetched('layoutList') ?? [];
            $listNow = $entity->get('layoutList') ?? [];

            foreach ($listBefore as $name) {
                if (!in_array($name, $listNow)) {
                    $layout = $this->entityManager
                        ->getRDBRepository(LayoutRecord::ENTITY_TYPE)
                        ->where([
                            'layoutSetId' => $entity->getId(),
                            'name' => $name,
                        ])
                        ->findOne();

                    if ($layout) {
                        $this->entityManager->removeEntity($layout);
                    }
                }
            }
        }
    }

    protected function afterRemove(Entity $entity, array $options = [])
    {
        parent::afterRemove($entity);

        $layoutList = $this->entityManager
            ->getRDBRepository(LayoutRecord::ENTITY_TYPE)
            ->where([
                'layoutSetId' => $entity->getId(),
            ])
            ->find();

        foreach ($layoutList as $layout) {
            $this->entityManager->removeEntity($layout);
        }
    }
}

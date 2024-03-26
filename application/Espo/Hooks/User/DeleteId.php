<?php


namespace Espo\Hooks\User;

use Espo\Core\Hook\Hook\BeforeRemove;
use Espo\Core\Hook\Hook\BeforeSave;
use Espo\Core\Utils\Util;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\RemoveOptions;
use Espo\ORM\Repository\Option\SaveOptions;


class DeleteId implements BeforeRemove, BeforeSave
{
    public function beforeRemove(Entity $entity, RemoveOptions $options): void
    {
        $entity->set('deleteId', Util::generateId());
    }

    public function beforeSave(Entity $entity, SaveOptions $options): void
    {
        if (!$entity->isAttributeChanged('deleted')) {
            return;
        }

        $deleteId = $entity->get('deleted') ? Util::generateId() : '0';

        $entity->set('deleteId', $deleteId);
    }
}

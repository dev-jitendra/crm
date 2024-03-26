<?php


namespace Espo\Services;

use Espo\ORM\Entity;
use Espo\Core\Exceptions\Forbidden;


class EmailFolder extends Record
{
    protected function beforeCreateEntity(Entity $entity, $data)
    {
        parent::beforeCreateEntity($entity, $data);

        if (!$this->user->isAdmin() || !$entity->get('assignedUserId')) {
            $entity->set('assignedUserId', $this->user->getId());
        }

        if (!$this->acl->checkEntityEdit($entity)) {
            throw new Forbidden();
        }
    }
}

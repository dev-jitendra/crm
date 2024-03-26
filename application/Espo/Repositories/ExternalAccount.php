<?php


namespace Espo\Repositories;

use Espo\ORM\Entity;

use Espo\Core\Repositories\Database;

use Espo\Entities\ExternalAccount as ExternalAccountEntity;


class ExternalAccount extends Database
{
    public function getById(string $id): ?Entity
    {
        $entity = parent::getById($id);

        if (!$entity) {
            
            $entity = $this->getNew();

            $entity->set('id', $id);
        }

        return $entity;
    }
}

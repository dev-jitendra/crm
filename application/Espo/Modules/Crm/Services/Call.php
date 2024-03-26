<?php


namespace Espo\Modules\Crm\Services;

use Espo\ORM\Entity;

class Call extends Meeting
{
    protected function afterUpdateEntity(Entity $entity, $data)
    {
        parent::afterUpdateEntity($entity, $data);

        if (isset($data->contactsIds) || isset($data->leadsIds)) {
            $this->loadAdditionalFields($entity);
        }
    }
}

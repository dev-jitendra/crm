<?php


namespace Espo\Repositories;

use Espo\ORM\Entity;

use Espo\Core\Utils\Util;
use Espo\Core\Repositories\Database;


class UniqueId extends Database
{
    protected $hooksDisabled = true;

    public function getNew(): Entity
    {
        $entity = parent::getNew();

        $entity->set('name', Util::generateMoreEntropyId());

        return $entity;
    }
}

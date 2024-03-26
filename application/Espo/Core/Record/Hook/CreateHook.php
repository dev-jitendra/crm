<?php


namespace Espo\Core\Record\Hook;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Conflict;
use Espo\Core\Exceptions\Forbidden;
use Espo\ORM\Entity;
use Espo\Core\Record\CreateParams;


interface CreateHook
{
    
    public function process(Entity $entity, CreateParams $params): void;
}

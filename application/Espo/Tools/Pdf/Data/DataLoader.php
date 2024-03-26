<?php


namespace Espo\Tools\Pdf\Data;

use Espo\ORM\Entity;
use Espo\Tools\Pdf\Params;

use stdClass;

interface DataLoader
{
    public function load(Entity $entity, Params $params): stdClass;
}

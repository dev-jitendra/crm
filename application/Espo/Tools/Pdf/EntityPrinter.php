<?php


namespace Espo\Tools\Pdf;

use Espo\ORM\Entity;

interface EntityPrinter
{
    public function print(Template $template, Entity $entity, Params $params, Data $data): Contents;
}

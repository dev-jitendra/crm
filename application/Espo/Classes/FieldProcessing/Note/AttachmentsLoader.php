<?php


namespace Espo\Classes\FieldProcessing\Note;

use Espo\ORM\Entity;
use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Entities\Note;


class AttachmentsLoader implements Loader
{
    public function process(Entity $entity, Params $params): void
    {
        
        $entity->loadAttachments();
    }
}

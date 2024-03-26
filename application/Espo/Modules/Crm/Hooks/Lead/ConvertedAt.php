<?php


namespace Espo\Modules\Crm\Hooks\Lead;

use Espo\Core\Field\DateTime;
use Espo\Core\Hook\Hook\BeforeSave;
use Espo\Modules\Crm\Entities\Lead;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\SaveOptions;


class ConvertedAt implements BeforeSave
{
    
    public function beforeSave(Entity $entity, SaveOptions $options): void
    {
        if (!$entity->isAttributeChanged('status')) {
            return;
        }

        if ($entity->getConvertedAt() ) {
            return;
        }

        if ($entity->getStatus() !== Lead::STATUS_CONVERTED) {
            return;
        }

        $entity->setValueObject('convertedAt', DateTime::createNow());
    }
}

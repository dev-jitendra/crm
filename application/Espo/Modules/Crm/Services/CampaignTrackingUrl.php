<?php


namespace Espo\Modules\Crm\Services;

use Espo\Core\Exceptions\Forbidden;
use Espo\ORM\Entity;

use Espo\Services\Record;


class CampaignTrackingUrl extends Record
{
    protected $mandatorySelectAttributeList = ['campaignId'];

    protected function beforeCreateEntity(Entity $entity, $data)
    {
        parent::beforeCreateEntity($entity, $data);

        if (!$this->getAcl()->check($entity, 'edit')) {
            throw new Forbidden();
        }
    }
}

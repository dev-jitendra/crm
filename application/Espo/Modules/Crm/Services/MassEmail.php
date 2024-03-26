<?php


namespace Espo\Modules\Crm\Services;

use Espo\Core\Acl\Table;
use Espo\Core\Exceptions\Forbidden;
use Espo\Modules\Crm\Entities\EmailQueueItem;
use Espo\ORM\Entity;
use Espo\Services\Record;


class MassEmail extends Record
{
    protected $mandatorySelectAttributeList = ['campaignId'];

    protected function beforeCreateEntity(Entity $entity, $data)
    {
        parent::beforeCreateEntity($entity, $data);

        if (!$this->acl->check($entity, Table::ACTION_EDIT)) {
            throw new Forbidden();
        }
    }

    protected function afterDeleteEntity(Entity $entity)
    {
        parent::afterDeleteEntity($entity);

        $delete = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from(EmailQueueItem::ENTITY_TYPE)
            ->where([
                 'massEmailId' => $entity->getId(),
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($delete);
    }
}

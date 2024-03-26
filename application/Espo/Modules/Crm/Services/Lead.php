<?php


namespace Espo\Modules\Crm\Services;

use Espo\Core\Utils\DateTime;
use Espo\Entities\Email;
use Espo\Modules\Crm\Entities\Campaign as CampaignEntity;
use Espo\Modules\Crm\Entities\CampaignLogRecord as CampaignLogRecordEntity;
use Espo\ORM\Entity;
use Espo\Modules\Crm\Entities\Lead as LeadEntity;
use Espo\Services\Record;
use Espo\Core\Di;


class Lead extends Record
{
    protected $linkMandatorySelectAttributeList = [
        'targetLists' => ['isOptedOut'],
    ];

    
    protected function afterCreateEntity(Entity $entity, $data)
    {
        if (!empty($data->emailId)) {
            
            $email = $this->entityManager->getEntityById(Email::ENTITY_TYPE, $data->emailId);

            if (
                $email &&
                !$email->getParentId() &&
                $this->acl->check($email)
            ) {
                $email->set([
                    'parentType' => LeadEntity::ENTITY_TYPE,
                    'parentId' => $entity->getId(),
                ]);

                $this->entityManager->saveEntity($email);
            }
        }

        $campaignLink = $entity->getCampaign();

        if ($campaignLink) {
            $campaign = $this->entityManager->getEntityById(CampaignEntity::ENTITY_TYPE, $campaignLink->getId());

            if ($campaign) {
                $log = $this->entityManager->getNewEntity(CampaignLogRecordEntity::ENTITY_TYPE);

                $log->set([
                    'action' => CampaignLogRecordEntity::ACTION_LEAD_CREATED,
                    'actionDate' => DateTime::getSystemNowString(),
                    'parentType' => LeadEntity::ENTITY_TYPE,
                    'parentId' => $entity->getId(),
                    'campaignId' => $campaign->getId(),
                ]);

                $this->entityManager->saveEntity($log);
            }
        }
    }
}

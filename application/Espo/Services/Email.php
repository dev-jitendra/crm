<?php


namespace Espo\Services;

use Espo\Core\Utils\SystemUser;
use Espo\Tools\Email\SendService;
use Espo\ORM\Entity;
use Espo\Entities\User;
use Espo\Entities\Email as EmailEntity;
use Espo\Tools\Email\InboxService;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Conflict;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Mail\Exceptions\SendingError;
use Espo\Core\Mail\Sender;
use Espo\Core\Mail\SmtpParams;
use Espo\Core\Record\CreateParams;

use Espo\Tools\Email\Util;
use stdClass;


class Email extends Record
{

    protected $getEntityBeforeUpdate = true;

    
    protected $allowedForUpdateFieldList = [
        'parent',
        'teams',
        'assignedUser',
    ];

    protected $mandatorySelectAttributeList = [
        'name',
        'createdById',
        'dateSent',
        'fromString',
        'fromEmailAddressId',
        'fromEmailAddressName',
        'parentId',
        'parentType',
        'isHtml',
        'isReplied',
        'status',
        'accountId',
        'folderId',
        'messageId',
        'sentById',
        'replyToString',
        'hasAttachment',
        'groupFolderId',
    ];

    private ?SendService $sendService = null;

    
    public function getUserSmtpParams(string $userId): ?SmtpParams
    {
        return $this->getSendService()->getUserSmtpParams($userId);
    }

    
    public function sendEntity(EmailEntity $entity, ?User $user = null): void
    {
        $this->getSendService()->send($entity, $user);
    }

    private function getSendService(): SendService
    {
        if (!$this->sendService) {
            $this->sendService = $this->injectableFactory->create(SendService::class);
        }

        return $this->sendService;
    }

    
    public function create(stdClass $data, CreateParams $params): Entity
    {
        
        $entity = parent::create($data, $params);

        if ($entity->getStatus() === EmailEntity::STATUS_SENDING) {
            $this->getSendService()->send($entity, $this->user);
        }

        return $entity;
    }

    protected function beforeCreateEntity(Entity $entity, $data)
    {
        

        if ($entity->getStatus() === EmailEntity::STATUS_SENDING) {
            $messageId = Sender::generateMessageId($entity);

            $entity->set('messageId', '<' . $messageId . '>');
        }
    }

    
    protected function afterUpdateEntity(Entity $entity, $data)
    {
        

        if ($entity->getStatus() === EmailEntity::STATUS_SENDING) {
            $this->getSendService()->send($entity, $this->user);
        }

        $this->loadAdditionalFields($entity);

        if (!isset($data->from) && !isset($data->to) && !isset($data->cc)) {
            $entity->clear('nameHash');
            $entity->clear('idHash');
            $entity->clear('typeHash');
        }
    }

    public function getEntity(string $id): ?Entity
    {
        
        $entity = parent::getEntity($id);

        if ($entity && !$entity->isRead()) {
            $this->markAsRead($entity->getId());
        }

        return $entity;
    }

    private function markAsRead(string $id, ?string $userId = null): void
    {
        $service = $this->injectableFactory->create(InboxService::class);

        $service->markAsRead($id, $userId);
    }

    
    static public function parseFromName(?string $string): string
    {
        return Util::parseFromName($string);
    }

    
    static public function parseFromAddress(?string $string): string
    {
        return Util::parseFromAddress($string);
    }

    protected function beforeUpdateEntity(Entity $entity, $data)
    {
        

        $skipFilter = false;

        if ($this->user->isAdmin()) {
            $skipFilter = true;
        }

        if ($this->isEmailManuallyArchived($entity)) {
            $skipFilter = true;
        }
        else if ($entity->isAttributeChanged('dateSent')) {
            $entity->set('dateSent', $entity->getFetched('dateSent'));
        }

        if ($entity->getStatus() === EmailEntity::STATUS_DRAFT) {
            $skipFilter = true;
        }

        if (
            $entity->getStatus() === EmailEntity::STATUS_SENDING &&
            $entity->getFetched('status') === EmailEntity::STATUS_DRAFT
        ) {
            $skipFilter = true;
        }

        if (
            $entity->isAttributeChanged('status') &&
            $entity->getFetched('status') === EmailEntity::STATUS_ARCHIVED
        ) {
            $entity->set('status', EmailEntity::STATUS_ARCHIVED);
        }

        if (!$skipFilter) {
            $this->clearEntityForUpdate($entity);
        }

        if ($entity->getStatus() == EmailEntity::STATUS_SENDING) {
            $messageId = Sender::generateMessageId($entity);

            $entity->set('messageId', '<' . $messageId . '>');
        }
    }

    private function isEmailManuallyArchived(EmailEntity $email): bool
    {
        if ($email->getStatus() !== EmailEntity::STATUS_ARCHIVED) {
            return false;
        }

        $userId = $email->getCreatedBy()?->getId();

        if (!$userId) {
            return false;
        }

        
        $user = $this->entityManager
            ->getRDBRepositoryByClass(User::class)
            ->getById($userId);

        if (!$user) {
            return true;
        }

        return $user->getUserName() !== SystemUser::NAME;
    }

    private function clearEntityForUpdate(EmailEntity $email): void
    {
        $fieldDefsList = $this->entityManager
            ->getDefs()
            ->getEntity(EmailEntity::ENTITY_TYPE)
            ->getFieldList();

        foreach ($fieldDefsList as $fieldDefs) {
            $field = $fieldDefs->getName();

            if ($fieldDefs->getParam('isCustom')) {
                continue;
            }

            if (in_array($field, $this->allowedForUpdateFieldList)) {
                continue;
            }

            $attributeList = $this->fieldUtil->getAttributeList(EmailEntity::ENTITY_TYPE, $field);

            foreach ($attributeList as $attribute) {
                $email->clear($attribute);
            }
        }
    }
}

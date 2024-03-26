<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

use Espo\Core\Field\DateTime;

use RuntimeException;
use stdClass;

class Note extends Entity
{
    public const ENTITY_TYPE = 'Note';

    public const TARGET_SELF = 'self';
    public const TARGET_ALL = 'all';
    public const TARGET_TEAMS = 'teams';
    public const TARGET_USERS = 'users';
    public const TARGET_PORTALS = 'portals';

    public const TYPE_POST = 'Post';
    public const TYPE_UPDATE = 'Update';
    public const TYPE_STATUS = 'Status';
    public const TYPE_CREATE = 'Create';
    public const TYPE_CREATE_RELATED = 'CreateRelated';
    public const TYPE_RELATE = 'Relate';
    public const TYPE_UNRELATE = 'Unrelate';
    public const TYPE_ASSIGN = 'Assign';
    public const TYPE_EMAIL_RECEIVED = 'EmailReceived';
    public const TYPE_EMAIL_SENT = 'EmailSent';

    private bool $aclIsProcessed = false;

    public function isPost(): bool
    {
        return $this->getType() === self::TYPE_POST;
    }

    public function isGlobal(): bool
    {
        return (bool) $this->get('isGlobal');
    }

    public function getType(): ?string
    {
        return $this->get('type');
    }

    public function getTargetType(): ?string
    {
        return $this->get('targetType');
    }

    public function getParentType(): ?string
    {
        return $this->get('parentType');
    }

    public function getParentId(): ?string
    {
        return $this->get('parentId');
    }

    public function getSuperParentType(): ?string
    {
        return $this->get('superParentType');
    }

    public function getSuperParentId(): ?string
    {
        return $this->get('superParentId');
    }

    public function getRelatedType(): ?string
    {
        return $this->get('relatedType');
    }

    public function getRelatedId(): ?string
    {
        return $this->get('relatedId');
    }

    public function getData(): stdClass
    {
        return $this->get('data') ?? (object) [];
    }

    public function isInternal(): bool
    {
        return (bool) $this->get('isInternal');
    }

    public function getPost(): ?string
    {
        return $this->get('post');
    }

    public function getCreatedAt(): ?DateTime
    {
        
        return $this->getValueObject('createdAt');
    }

    public function setAclIsProcessed(): void
    {
        $this->aclIsProcessed = true;
    }

    public function isAclProcessed(): bool
    {
        return (bool) $this->aclIsProcessed;
    }

    public function loadAttachments(): void
    {
        $data = $this->get('data');

        if (empty($data) || empty($data->attachmentsIds) || !is_array($data->attachmentsIds)) {
            $this->loadLinkMultipleField('attachments');

            return;
        }

        if (!$this->entityManager) {
            throw new RuntimeException();
        }

        $attachmentsIds = $data->attachmentsIds;

        $collection = $this->entityManager
            ->getRDBRepository(Attachment::ENTITY_TYPE)
            ->select(['id', 'name', 'type'])
            ->order('createdAt')
            ->where([
                'id' => $attachmentsIds
            ])
            ->find();

        $ids = [];

        $names = (object) [];
        $types = (object) [];

        foreach ($collection as $e) {
            $id = $e->getId();

            $ids[] = $id;

            $names->$id = $e->get('name');
            $types->$id = $e->get('type');
        }

        $this->set('attachmentsIds', $ids);
        $this->set('attachmentsNames', $names);
        $this->set('attachmentsTypes', $types);
    }

    public function addNotifiedUserId(string $userId): void
    {
        $userIdList = $this->get('notifiedUserIdList');

        if (!is_array($userIdList)) {
            $userIdList = [];
        }

        if (!in_array($userId, $userIdList)) {
            $userIdList[] = $userId;
        }

        $this->set('notifiedUserIdList', $userIdList);
    }

    public function isUserIdNotified(string $userId): bool
    {
        $userIdList = $this->get('notifiedUserIdList') ?? [];

        return in_array($userId, $userIdList);
    }

    public function getCreatedById(): ?string
    {
        return $this->get('createdById');
    }

    public function loadAdditionalFields(): void
    {
        if (
            $this->getType() == self::TYPE_POST ||
            $this->getType() == self::TYPE_EMAIL_RECEIVED
        ) {
            $this->loadAttachments();
        }

        if ($this->getParentId() && $this->getParentType()) {
            $this->loadParentNameField('parent');
        }

        if ($this->getRelatedId() && $this->getRelatedType()) {
            $this->loadParentNameField('related');
        }

        if (
            $this->getType() == self::TYPE_POST &&
            $this->getParentId() === null &&
            !$this->get('isGlobal')
        ) {
            $targetType = $this->getTargetType();

            if (
                !$targetType ||
                $targetType === self::TARGET_USERS ||
                $targetType === self::TARGET_SELF
            ) {
                $this->loadLinkMultipleField('users');
            }

            if (
                $targetType !== self::TARGET_USERS &&
                $targetType !== self::TARGET_SELF
            ) {
                if (!$targetType || $targetType === self::TARGET_TEAMS) {
                    $this->loadLinkMultipleField('teams');
                }
                else if ($targetType === self::TARGET_PORTALS) {
                    $this->loadLinkMultipleField('portals');
                }
            }
        }
    }
}

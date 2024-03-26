<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\ORM\Entity;

class EmailQueueItem extends Entity
{
    public const ENTITY_TYPE = 'EmailQueueItem';

    public const STATUS_PENDING = 'Pending';
    public const STATUS_FAILED = 'Failed';
    public const STATUS_SENT = 'Sent';
    public const STATUS_SENDING = 'Sending';

    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    public function getAttemptCount(): int
    {
        return (int) $this->get('attemptCount');
    }

    public function isTest(): bool
    {
        return (bool) $this->get('isTest');
    }

    public function getTargetType(): string
    {
        $value = $this->get('targetType');

        if (!is_string($value)) {
            throw new \UnexpectedValueException();
        }

        return $value;
    }

    public function getTargetId(): string
    {
        $value = $this->get('targetId');

        if (!is_string($value)) {
            throw new \UnexpectedValueException();
        }

        return $value;
    }

    public function getMassEmailId(): ?string
    {
        return $this->get('massEmailId');
    }

    public function getEmailAddress(): ?string
    {
        return $this->get('emailAddress');
    }
}

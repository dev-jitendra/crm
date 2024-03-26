<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;
use Espo\Core\Field\DateTime;
use Espo\Core\Field\Link;

use Espo\Tools\Export\Params;

use RuntimeException;

class Export extends Entity
{
    public const ENTITY_TYPE = 'Export';

    public const STATUS_PENDING = 'Pending';

    public const STATUS_RUNNING = 'Running';

    public const STATUS_SUCCESS = 'Success';

    public const STATUS_FAILED = 'Failed';

    public function getParams(): Params
    {
        $raw = $this->get('params');

        if (!is_string($raw)) {
            throw new RuntimeException("No 'params'.");
        }

        
        $params = unserialize(base64_decode($raw));

        return $params;
    }

    public function getStatus(): string
    {
        $value = $this->get('status');

        if (!is_string($value)) {
            throw new RuntimeException("No 'status'.");
        }

        return $value;
    }

    public function getAttachmentId(): ?string
    {
        
        return $this->get('attachmentId');
    }

    public function notifyOnFinish(): bool
    {
        return (bool) $this->get('notifyOnFinish');
    }

    public function getCreatedAt(): DateTime
    {
        $value = $this->getValueObject('createdAt');

        if (!$value instanceof DateTime) {
            throw new RuntimeException("No 'createdAt'.");
        }

        return $value;
    }

    public function getCreatedBy(): Link
    {
        $value = $this->getValueObject('createdBy');

        if (!$value instanceof Link) {
            throw new RuntimeException("No 'createdBy'.");
        }

        return $value;
    }

    public function setStatus(string $status): self
    {
        $this->set('status', $status);

        return $this;
    }

    public function setAttachmentId(string $attachmentId): self
    {
        $this->set('attachmentId', $attachmentId);

        return $this;
    }

    public function setNotifyOnFinish(bool $notifyOnFinish = true): self
    {
        $this->set('notifyOnFinish', $notifyOnFinish);

        return $this;
    }
}

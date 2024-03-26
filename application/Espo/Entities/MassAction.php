<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;
use Espo\Core\Field\DateTime;
use Espo\Core\Field\Link;

use Espo\Core\MassAction\Data;
use Espo\Core\MassAction\Params;

use RuntimeException;

use stdClass;

class MassAction extends Entity
{
    public const ENTITY_TYPE = 'MassAction';

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

    public function getData(): Data
    {
        $raw = $this->get('data');

        if (!$raw instanceof stdClass) {
            throw new RuntimeException("No 'data'.");
        }

        return Data::fromRaw($raw);
    }

    public function getTargetEntityType(): string
    {
        $value = $this->get('entityType');

        if (!is_string($value)) {
            throw new RuntimeException("No 'entityType'.");
        }

        return $value;
    }

    public function getAction(): string
    {
        $value = $this->get('action');

        if (!is_string($value)) {
            throw new RuntimeException("No 'action'.");
        }

        return $value;
    }

    public function getStatus(): string
    {
        $value = $this->get('status');

        if (!is_string($value)) {
            throw new RuntimeException("No 'status'.");
        }

        return $value;
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

    public function getProcessedCount(): int
    {
        return (int) $this->get('processedCount');
    }

    public function setStatus(string $status): self
    {
        $this->set('status', $status);

        return $this;
    }

    public function setProcessedCount(int $processedCount): self
    {
        $this->set('processedCount', $processedCount);

        return $this;
    }

    public function setNotifyOnFinish(bool $notifyOnFinish = true): self
    {
        $this->set('notifyOnFinish', $notifyOnFinish);

        return $this;
    }
}

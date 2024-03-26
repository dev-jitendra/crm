<?php


namespace Espo\Core\Mail\Account;

use Espo\Core\Utils\ObjectUtil;
use Espo\Core\Field\DateTime;

use stdClass;
use RuntimeException;

class FetchData
{
    private stdClass $data;

    public function __construct(stdClass $data)
    {
        $this->data = ObjectUtil::clone($data);
    }

    public static function fromRaw(stdClass $data): self
    {
        return new self($data);
    }

    public function getRaw(): stdClass
    {
        return ObjectUtil::clone($this->data);
    }

    public function getLastUniqueId(string $folder): ?string
    {
        return $this->data->lastUID->$folder ?? null;
    }

    public function getLastDate(string $folder): ?DateTime
    {
        $value = $this->data->lastDate->$folder ?? null;

        if ($value === null) {
            return null;
        }

        
        if ($value === 0) {
            return null;
        }

        if (!is_string($value)) {
            throw new RuntimeException("Bad value in fetch-data.");
        }

        return DateTime::fromString($value);
    }

    public function getForceByDate(string $folder): bool
    {
        return $this->data->byDate->$folder ?? false;
    }

    public function setLastUniqueId(string $folder, ?string $uniqueId): void
    {
        if (!property_exists($this->data, 'lastUID')) {
            $this->data->lastUID = (object) [];
        }

        $this->data->lastUID->$folder = $uniqueId;
    }

    public function setLastDate(string $folder, ?DateTime $lastDate): void
    {
        if (!property_exists($this->data, 'lastDate')) {
            $this->data->lastDate = (object) [];
        }

        if ($lastDate === null) {
            $this->data->lastDate->$folder = null;

            return;
        }

        $this->data->lastDate->$folder = $lastDate->toString();
    }

    public function setForceByDate(string $folder, bool $forceByDate): void
    {
        if (!property_exists($this->data, 'byDate')) {
            $this->data->byDate = (object) [];
        }

        $this->data->byDate->$folder = $forceByDate;
    }
}

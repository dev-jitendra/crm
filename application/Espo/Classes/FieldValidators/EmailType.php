<?php


namespace Espo\Classes\FieldValidators;

use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;

use stdClass;

class EmailType
{
    private Metadata $metadata;

    private const DEFAULT_MAX_LENGTH = 255;

    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }
    public function checkRequired(Entity $entity, string $field): bool
    {
        if ($this->isNotEmpty($entity, $field)) {
            return true;
        }

        $dataList = $entity->get($field . 'Data');

        if (!is_array($dataList)) {
            return false;
        }

        foreach ($dataList as $item) {
            if (!empty($item->emailAddress)) {
                return true;
            }
        }

        return false;
    }

    public function checkEmailAddress(Entity $entity, string $field): bool
    {
        if ($this->isNotEmpty($entity, $field)) {
            $address = $entity->get($field);

            if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }

        $dataList = $entity->get($field . 'Data');

        if (!is_array($dataList)) {
            return true;
        }

        foreach ($dataList as $item) {
            if (!$item instanceof stdClass) {
                return false;
            }

            if (empty($item->emailAddress)) {
                continue;
            }

            $address = $item->emailAddress;

            if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }

        return true;
    }

    public function checkMaxLength(Entity $entity, string $field): bool
    {
        
        $value = $entity->get($field);

        
        $maxLength = $this->metadata->get(['entityDefs', 'EmailAddress', 'fields', 'name', 'maxLength']) ??
            self::DEFAULT_MAX_LENGTH;

        if ($value && mb_strlen($value) > $maxLength) {
            return false;
        }

        $dataList = $entity->get($field . 'Data');

        if (!is_array($dataList)) {
            return true;
        }

        foreach ($dataList as $item) {
            $value = $item->emailAddress;

            if ($value && mb_strlen($value) > $maxLength) {
                return false;
            }
        }

        return true;
    }

    protected function isNotEmpty(Entity $entity, string $field): bool
    {
        return $entity->has($field) && $entity->get($field) !== '' && $entity->get($field) !== null;
    }
}

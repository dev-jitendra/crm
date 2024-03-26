<?php


namespace Espo\Classes\FieldValidators;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Defs;
use Espo\ORM\Entity;

use stdClass;


class PhoneType
{
    private const DEFAULT_MAX_LENGTH = 36;

    public function __construct(
        private Metadata $metadata,
        private Defs $defs,
        private Config $config
    ) {}

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
            if (!empty($item->phoneNumber)) {
                return true;
            }
        }

        return false;
    }

    public function checkValid(Entity $entity, string $field): bool
    {
        if ($this->isNotEmpty($entity, $field)) {
            $number = $entity->get($field);

            if (!$this->isValidNumber($number)) {
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

            $number = $item->phoneNumber ?? null;
            $type = $item->type ?? null;

            if (!$number) {
                return false;
            }

            if (!$this->isValidNumber($number)) {
                return false;
            }

            if (!$this->isValidType($entity->getEntityType(), $field, $type)) {
                return false;
            }
        }

        return true;
    }

    public function checkMaxLength(Entity $entity, string $field): bool
    {
        
        $value = $entity->get($field);

        
        $maxLength = $this->metadata->get(['entityDefs', 'PhoneNumber', 'fields', 'name', 'maxLength']) ??
            self::DEFAULT_MAX_LENGTH;

        if ($value && mb_strlen($value) > $maxLength) {
            return false;
        }

        $dataList = $entity->get($field . 'Data');

        if (!is_array($dataList)) {
            return true;
        }

        foreach ($dataList as $item) {
            $value = $item->phoneNumber;

            if ($value && mb_strlen($value) > $maxLength) {
                return false;
            }
        }

        return true;
    }

    
    private function isValidType(string $entityType, string $field, $type): bool
    {
        if ($type === null) {
            
            return true;
        }

        if (!is_string($type)) {
            return false;
        }

        
        $typeList = $this->defs
            ->getEntity($entityType)
            ->getField($field)
            ->getParam('typeList');

        if ($typeList === null) {
            return true;
        }

        
        if ($typeList === false) {
            return true;
        }

        return in_array($type, $typeList);
    }

    
    private function isValidNumber($number): bool
    {
        if (!is_string($number)) {
            return false;
        }

        if ($number === '') {
            return false;
        }

        $pattern = $this->metadata->get(['app', 'regExpPatterns', 'phoneNumberLoose', 'pattern']);

        if (!$pattern) {
            return true;
        }

        $preparedPattern = '/^' . $pattern . '$/';

        if (!preg_match($preparedPattern, $number)) {
            return false;
        }

        if (!$this->config->get('phoneNumberInternational')) {
            return true;
        }

        try {
            $numberObj = PhoneNumber::parse($number);
        }
        catch (PhoneNumberParseException) {
            return false;
        }

        if ((string) $numberObj !== $number) {
            return false;
        }

        return $numberObj->isPossibleNumber();
    }

    protected function isNotEmpty(Entity $entity, string $field): bool
    {
        return $entity->has($field) && $entity->get($field) !== '' && $entity->get($field) !== null;
    }
}

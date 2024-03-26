<?php


namespace Espo\Tools\EmailTemplate;

use Espo\ORM\Entity;

use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DateTime as DateTimeUtil;
use Espo\Core\Utils\NumberUtil;
use Espo\Core\Utils\Language;

class Formatter
{
    private Metadata $metadata;
    private Config $config;
    private DateTimeUtil $dateTime;
    private NumberUtil $number;
    private Language $language;

    public function __construct(
        Metadata $metadata,
        Config $config,
        DateTimeUtil $dateTime,
        NumberUtil $number,
        Language $language
    ) {
        $this->metadata = $metadata;
        $this->config = $config;
        $this->dateTime = $dateTime;
        $this->number = $number;
        $this->language = $language;
    }

    public function formatAttributeValue(Entity $entity, string $attribute, bool $isPlainText = false): ?string
    {
        $value = $entity->get($attribute);

        $fieldType = $this->metadata
            ->get(['entityDefs', $entity->getEntityType(), 'fields', $attribute, 'type']);

        $attributeType = $entity->getAttributeType($attribute);

        if ($fieldType === 'enum') {
            if ($value === null) {
                return '';
            }

            $label = $this->language->translateOption($value, $attribute, $entity->getEntityType());

            $translationPath = $this->metadata->get(
                ['entityDefs', $entity->getEntityType(), 'fields', $attribute, 'translation']
            );

            if ($translationPath) {
                $label = $this->language->get($translationPath . '.' . $value, $label);
            }

            return $label;
        }

        if ($fieldType === 'array' || $fieldType === 'multiEnum' || $fieldType === 'checklist') {
            $valueList = [];

            if (!is_array($value)) {
                return '';
            }

            foreach ($value as $v) {
                $valueList[] = $this->language->translateOption($v, $attribute, $entity->getEntityType());
            }

            return implode(', ', $valueList);
        }

        if ($attributeType === 'date') {
            if (!$value) {
                return '';
            }

            return $this->dateTime->convertSystemDate($value);
        }

        if ($attributeType === 'datetime') {
            if (!$value) {
                return '';
            }

            return $this->dateTime->convertSystemDateTime($value);
        }

        if ($attributeType === 'text') {
            if (!is_string($value)) {
                return '';
            }

            if ($fieldType === 'wysiwyg') {
                return $value;
            }

            if ($isPlainText) {
                return $value;
            }

            return nl2br($value);
        }

        if ($attributeType === 'float') {
            if (!is_float($value)) {
                return '';
            }

            $decimalPlaces = 2;

            if ($fieldType === 'currency') {
                $decimalPlaces = $this->config->get('currencyDecimalPlaces');
            }

            return $this->number->format($value, $decimalPlaces);
        }

        if ($attributeType === 'int') {
            if (!is_int($value)) {
                return '';
            }

            if (
                $fieldType === 'autoincrement' ||
                $fieldType === 'int' &&
                $this->metadata
                    ->get(['entityDefs', $entity->getEntityType(), 'fields', $attribute, 'disableFormatting'])
            ) {
                return (string) $value;
            }

            return $this->number->format($value);
        }

        if (!is_string($value) && is_scalar($value) || is_callable([$value, '__toString'])) {
            return strval($value);
        }

        if ($value === null) {
            return '';
        }

        if (!is_string($value)) {
            return null;
        }

        return $value;
    }
}

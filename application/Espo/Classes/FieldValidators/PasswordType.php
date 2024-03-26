<?php


namespace Espo\Classes\FieldValidators;

use stdClass;

class PasswordType
{
    private const DEFAULT_MAX_LENGTH = 255;

    public function rawCheckValid(stdClass $data, string $field): bool
    {
        $value = $data->$field ?? null;

        if ($value === null) {
            return true;
        }

        return is_string($value);
    }

    public function rawCheckMaxLength(stdClass $data, string $field, ?int $validationValue): bool
    {
        $value = $data->$field ?? null;

        if (!is_string($value)) {
            return true;
        }

        $maxLength = $validationValue ?? self::DEFAULT_MAX_LENGTH;

        if (mb_strlen($value) > $maxLength) {
            return false;
        }

        return true;
    }
}

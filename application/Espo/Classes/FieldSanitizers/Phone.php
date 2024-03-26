<?php


namespace Espo\Classes\FieldSanitizers;

use Espo\Core\FieldSanitize\Sanitizer;
use Espo\Core\FieldSanitize\Sanitizer\Data;
use Espo\Core\PhoneNumber\Sanitizer as PhoneNumberSanitizer;
use stdClass;

class Phone implements Sanitizer
{
    public function __construct(
        private PhoneNumberSanitizer $phoneNumberSanitizer
    ) {}

    public function sanitize(Data $data, string $field): void
    {
        $number = $data->get($field);

        if ($number !== null) {
            $number = $this->phoneNumberSanitizer->sanitize($number);

            $data->set($field, $number);
        }

        $items = $data->get($field . 'Data');

        if (!is_array($items)) {
            return;
        }

        foreach ($items as $item) {
            if (!$item instanceof stdClass) {
                continue;
            }

            $number = $item->phoneNumber ?? null;

            if (!is_scalar($number)) {
                continue;
            }

            $number = (string) $number;

            $item->phoneNumber = $this->phoneNumberSanitizer->sanitize($number);
        }

        $data->set($field . 'Data', $items);
    }
}

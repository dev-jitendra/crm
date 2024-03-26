<?php


namespace Espo\Core\PhoneNumber;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Espo\Core\Utils\Config;

class Sanitizer
{
    public function __construct(
        private Config $config
    ) {}

    public function sanitize(string $value, ?string $countryCode = null): string
    {
        $value = trim($value);

        if (str_starts_with($value, '+')) {
            if ($this->config->get('phoneNumberInternational')) {
                return $this->parsePhoneNumber($value, null);
            }

            return $value;
        }

        if (!$countryCode) {
            return $value;
        }

        $code = strtoupper($countryCode);

        return $this->parsePhoneNumber($value, $code);
    }

    private function parsePhoneNumber(string $value, ?string $countryCode): string
    {
        try {
            $number = PhoneNumber::parse($value, $countryCode);

            return (string) $number;
        }
        catch (PhoneNumberParseException) {
            return $value;
        }
    }
}

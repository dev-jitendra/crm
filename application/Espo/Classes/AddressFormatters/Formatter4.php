<?php


namespace Espo\Classes\AddressFormatters;

use Espo\Core\Field\Address;
use Espo\Core\Field\Address\AddressFormatter;

class Formatter4 implements AddressFormatter
{
    public function format(Address $address): string
    {
        $result = '';

        $street = $address->getStreet();
        $city = $address->getCity();
        $country = $address->getCountry();
        $state = $address->getState();
        $postalCode = $address->getPostalCode();

        if ($street) {
            $result .= $street;
        }

        if ($city) {
            if ($result) {
                $result .= "\n";
            }

            $result .= $city;
        }

        if ($country || $state || $postalCode) {
            if ($result) {
                $result .= "\n";
            }

            if ($country) {
                $result .= $country;
            }

            if ($state && $country) {
                $result .= ' - ';
            }

            if ($state) {
                $result .= $state;
            }

            if ($postalCode && ($state || $country)) {
                $result .= ' ';
            }

            if ($postalCode) {
                $result .= $postalCode;
            }
        }

        return $result;
    }
}

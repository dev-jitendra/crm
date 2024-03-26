<?php


namespace Espo\Classes\AddressFormatters;

use Espo\Core\Field\Address;
use Espo\Core\Field\Address\AddressFormatter;

class Formatter1 implements AddressFormatter
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

        if ($city || $state || $postalCode) {
            if ($result) {
                $result .= "\n";
            }

            if ($city) {
                $result .= $city;
            }

            if ($state && $city) {
                $result .= ', ';
            }

            if ($state) {
                $result .= $state;
            }

            if ($postalCode && ($state || $city)) {
                $result .= ' ';
            }

            if ($postalCode) {
                $result .= $postalCode;
            }
        }

        if ($country) {
            if ($result) {
                $result .= "\n";
            }

            $result .= $country;
        }

        return $result;
    }
}

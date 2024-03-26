<?php


namespace Espo\Classes\AddressFormatters;

use Espo\Core\Field\Address;
use Espo\Core\Field\Address\AddressFormatter;

class Formatter2 implements AddressFormatter
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

        if ($city || $postalCode) {
            if ($result) {
                $result .= "\n";
            }

            if ($postalCode) {
                $result .= $postalCode;
            }

            if ($postalCode && $city) {
                $result .= ' ';
            }

            if ($city) {
                $result .= $city;
            }
        }

        if ($state || $country) {
            if ($result) {
                $result .= "\n";
            }

            if ($state) {
                $result .= $state;
            }

            if ($state && $country) {
                $result .= ' ';
            }

            if ($country) {
                $result .= $country;
            }
        }

        return $result;
    }
}

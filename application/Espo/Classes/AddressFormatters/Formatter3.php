<?php


namespace Espo\Classes\AddressFormatters;

use Espo\Core\Field\Address;
use Espo\Core\Field\Address\AddressFormatter;

class Formatter3 implements AddressFormatter
{
    public function format(Address $address): string
    {
        $result = '';

        $street = $address->getStreet();
        $city = $address->getCity();
        $country = $address->getCountry();
        $state = $address->getState();
        $postalCode = $address->getPostalCode();

        if ($country) {
            $result .= $country;
        }

        if ($city || $state || $postalCode) {
            if ($result) {
                $result .= "\n";
            }

            if ($state) {
                $result .= $state;
            }

            if ($state && $postalCode) {
                $result .= ' ';
            }

            if ($postalCode) {
                $result .= $postalCode;
            }

            if ($city && ($state || $postalCode)) {
                $result .= ' ';
            }

            if ($city) {
                $result .= $city;
            }
        }

        if ($street) {
            if ($result) {
                $result .= "\n";
            }

            $result .= $street;
        }

        return $result;
    }
}

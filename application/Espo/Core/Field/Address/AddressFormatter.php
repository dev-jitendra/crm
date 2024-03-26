<?php


namespace Espo\Core\Field\Address;

use Espo\Core\Field\Address;


interface AddressFormatter
{
    public function format(Address $address): string;
}

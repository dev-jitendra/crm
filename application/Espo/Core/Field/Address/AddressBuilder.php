<?php


namespace Espo\Core\Field\Address;

use Espo\Core\Field\Address;


class AddressBuilder
{
    private ?string $street;

    private ?string$city;

    private ?string $country;

    private ?string $state;

    private ?string $postalCode;

    public function clone(Address $address): self
    {
        $this->setStreet($address->getStreet());
        $this->setCity($address->getCity());
        $this->setCountry($address->getCountry());
        $this->setState($address->getState());
        $this->setPostalCode($address->getPostalCode());

        return $this;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function build(): Address
    {
        return new Address(
            $this->country,
            $this->state,
            $this->city,
            $this->street,
            $this->postalCode
        );
    }
}

<?php


namespace Espo\Core\Field;

use Espo\Core\Field\Address\AddressBuilder;


class Address
{
    public function __construct(
        private ?string $country = null,
        private ?string $state = null,
        private ?string $city = null,
        private ?string $street = null,
        private ?string $postalCode = null
    ) {}

    
    public function hasStreet(): bool
    {
        return $this->street !== null;
    }

    
    public function hasCity(): bool
    {
        return $this->city !== null;
    }

    
    public function hasCountry(): bool
    {
        return $this->country !== null;
    }

    
    public function hasState(): bool
    {
        return $this->state !== null;
    }

    
    public function hasPostalCode(): bool
    {
        return $this->postalCode !== null;
    }

    
    public function getStreet(): ?string
    {
        return $this->street;
    }

    
    public function getCity(): ?string
    {
        return $this->city;
    }

    
    public function getCountry(): ?string
    {
        return $this->country;
    }

    
    public function getState(): ?string
    {
        return $this->state;
    }

    
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    
    public function withStreet(?string $street): self
    {
        return self::createBuilder()
            ->clone($this)
            ->setStreet($street)
            ->build();
    }

    
    public function withCity(?string $city): self
    {
        return self::createBuilder()
            ->clone($this)
            ->setCity($city)
            ->build();
    }

    
    public function withCountry(?string $country): self
    {
        return self::createBuilder()
            ->clone($this)
            ->setCountry($country)
            ->build();
    }

    
    public function withState(?string $state): self
    {
        return self::createBuilder()
            ->clone($this)
            ->setState($state)
            ->build();
    }

    
    public function withPostalCode(?string $postalCode): self
    {
        return self::createBuilder()
            ->clone($this)
            ->setPostalCode($postalCode)
            ->build();
    }

    
    public static function create(): self
    {
        return new self();
    }

    
    public static function createBuilder(): AddressBuilder
    {
        return new AddressBuilder();
    }
}

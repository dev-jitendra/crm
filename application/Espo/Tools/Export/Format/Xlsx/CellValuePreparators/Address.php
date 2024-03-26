<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\Core\Field\Address\AddressFactory;
use Espo\Core\Field\Address\AddressFormatterFactory;
use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

class Address implements CellValuePreparator
{
    public function __construct(
        private AddressFormatterFactory $formatterFactory
    ) {}

    public function prepare(Entity $entity, string $name): ?string
    {
        $address = (new AddressFactory())->createFromEntity($entity, $name);

        $formatter = $this->formatterFactory->createDefault();

        return $formatter->format($address) ?: null;
    }
}

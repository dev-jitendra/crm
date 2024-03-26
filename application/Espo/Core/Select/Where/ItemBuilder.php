<?php


namespace Espo\Core\Select\Where;

use Espo\Core\Select\Where\Item\Data;


class ItemBuilder
{
    private ?string $type = null;
    private ?string $attribute = null;
    
    private $value = null;
    private ?Data $data = null;

    public static function create(): self
    {
        return new self();
    }

    
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    
    public function setAttribute(?string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    
    public function setData(?Data $data): self
    {
        $this->data = $data;

        return $this;
    }

    
    public function setItemList(array $itemList): self
    {
        $this->value = array_map(
            function (Item $item): array {
                return $item->getRaw();
            },
            $itemList
        );

        return $this;
    }

    public function build(): Item
    {
        return Item
            ::fromRaw([
                'type' => $this->type,
                'attribute' => $this->attribute,
                'value' => $this->value,
            ])
            ->withData($this->data);
    }
}

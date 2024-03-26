<?php


namespace Espo\Core\Select\Where;

use Espo\Core\Select\Where\Item\Data;
use Espo\Core\Select\Where\Item\Data\DateTime as DateTimeData;

use InvalidArgumentException;
use RuntimeException;


class Item
{
    public const TYPE_AND = Item\Type::AND;
    public const TYPE_OR = Item\Type::OR;

    private ?string $attribute = null;
    private mixed $value = null;
    private ?Data $data = null;

    
    private $noAttributeTypeList = [
        Item\Type::AND,
        Item\Type::OR,
        Item\Type::NOT,
        Item\Type::SUBQUERY_IN,
        Item\Type::SUBQUERY_NOT_IN,
    ];

    
    private $withNestedItemsTypeList = [
        Item\Type::AND,
        Item\Type::OR,
    ];

    private function __construct(private string $type)
    {}

    
    public static function fromRaw(array $params): self
    {
        $type = $params['type'] ?? null;

        if (!$type) {
            throw new InvalidArgumentException("No 'type' in where item.");
        }

        $obj = new self($type);

        $obj->attribute = $params['attribute'] ?? $params['field'] ?? null;
        $obj->value = $params['value'] ?? null;

        if ($params['dateTime'] ?? false) {
            $obj->data = DateTimeData
                ::create()
                ->withTimeZone($params['timeZone'] ?? null);
        }

        unset($params['field']);
        unset($params['dateTime']);
        unset($params['timeZone']);

        foreach (array_keys($params) as $key) {
            if (!property_exists($obj, $key)) {
                throw new InvalidArgumentException("Unknown parameter '{$key}'.");
            }
        }

        if (
            !$obj->attribute &&
            !in_array($obj->type, $obj->noAttributeTypeList)
        ) {
            throw new InvalidArgumentException("No 'attribute' in where item.");
        }

        if (in_array($obj->type, $obj->withNestedItemsTypeList)) {
            $obj->value = $obj->value ?? [];

            if (
                !is_array($obj->value) ||
                count($obj->value) && array_keys($obj->value) !== range(0, count($obj->value) - 1)
            ) {
                throw new InvalidArgumentException("Bad 'value'.");
            }
        }

        return $obj;
    }

    
    public static function fromRawAndGroup(array $paramList): self
    {
        return self::fromRaw([
            'type' => Item\Type::AND,
            'value' => $paramList,
        ]);
    }

    
    public function getRaw(): array
    {
        $type = $this->type;

        $raw = [
            'type' => $type,
            'value' => $this->value,
        ];

        if ($this->attribute) {
            $raw['attribute'] = $this->attribute;
        }

        if ($this->data instanceof DateTimeData) {
            $raw['dateTime'] = true;

            $timeZone = $this->data->getTimeZone();

            if ($timeZone) {
                $raw['timeZone'] = $timeZone;
            }
        }

        return $raw;
    }

    
    public function getType(): string
    {
        return $this->type;
    }

    
    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    
    public function getValue()
    {
        return $this->value;
    }

    
    public function getItemList(): array
    {
        if (!in_array($this->type, $this->withNestedItemsTypeList)) {
            throw new RuntimeException("Nested items not supported for '{$this->type}' type.");
        }

        $list = [];

        foreach ($this->value as $raw) {
            $list[] = Item::fromRaw($raw);
        }

        return $list;
    }

    
    public function getData(): ?Data
    {
        return $this->data;
    }

    
    public static function createBuilder(): ItemBuilder
    {
        return new ItemBuilder();
    }

    
    public function withData(?Data $data): self
    {
        $obj = clone $this;
        $obj->data = $data;

        return $obj;
    }
}

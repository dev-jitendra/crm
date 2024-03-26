<?php


namespace Espo\Tools\MassUpdate;

use Espo\Core\MassAction\Data as ActionData;
use Espo\Core\Utils\ObjectUtil;

use RuntimeException;
use stdClass;

class Data
{
    private stdClass $values;

    
    private array $actions;

    
    private function __construct(stdClass $values, array $actions)
    {
        $this->values = $values;
        $this->actions = $actions;
    }

    public function has(string $attribute): bool
    {
        return property_exists($this->values, $attribute);
    }

    
    public function getAttributeList(): array
    {
        return array_keys(get_object_vars($this->values));
    }

    
    public function getValue(string $attribute)
    {
        return $this->getValues()->$attribute ?? null;
    }

    public function getValues(): stdClass
    {
        return ObjectUtil::clone($this->values);
    }

    
    public function getAction(string $attribute): ?string
    {
        if (!$this->has($attribute)) {
            return null;
        }

        return $this->actions[$attribute] ?? Action::UPDATE;
    }

    public static function create(): self
    {
        return new self((object) [], []);
    }

    public static function fromMassActionData(ActionData $data): self
    {
        $values = $data->get('values');
        $rawActions = $data->get('actions');

        
        if (!$data->has('values')) {
            return new self($data->getRaw(), []);
        }

        if (!$values instanceof stdClass) {
            throw new RuntimeException("No `values` in mass-action data.");
        }

        if ($rawActions !== null && !$rawActions instanceof stdClass) {
            throw new RuntimeException("Bad `actions` in mass-action data.");
        }

        if ($rawActions === null) {
            $rawActions = (object) [];
        }

        return new self($values, get_object_vars($rawActions));
    }

    
    public function with(string $attribute, $value, ?string $action = null): self
    {
        if ($action === null) {
            $action = $this->getAction($attribute) ?? Action::UPDATE;
        }

        $values = $this->getValues();
        $actions = $this->actions;

        $values->$attribute = $value;
        $actions[$attribute] = $action;

        return new self($values, $actions);
    }

    public function without(string $attribute): self
    {
        $values = $this->getValues();
        $actions = $this->actions;

        unset($values->$attribute);
        unset($actions[$attribute]);

        return new self($values, $actions);
    }

    public function toMassActionData(): ActionData
    {
        return ActionData::fromRaw((object) [
            'values' => $this->getValues(),
            'actions' => (object) $this->actions,
        ]);
    }

    public function __clone()
    {
        $this->values = ObjectUtil::clone($this->values);
    }
}

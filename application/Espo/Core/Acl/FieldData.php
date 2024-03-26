<?php


namespace Espo\Core\Acl;

use stdClass;
use RuntimeException;


class FieldData
{
    
    private $actionData = [];

    private function __construct() {}

    
    public function __get(string $name)
    {
        throw new RuntimeException("Accessing ScopeData properties is not allowed.");
    }

    
    public function get(string $action): string
    {
        return $this->actionData[$action] ?? Table::LEVEL_NO;
    }

    
    public function getRead(): string
    {
        return $this->get(Table::ACTION_READ);
    }

    
    public function getEdit(): string
    {
        return $this->get(Table::ACTION_EDIT);
    }

    
    public static function fromRaw(stdClass $raw): self
    {
        $obj = new self();

        $obj->actionData = get_object_vars($raw);

        foreach ($obj->actionData as $item) {
            if (!is_string($item)) {
                throw new RuntimeException("Bad raw scope data.");
            }
        }

        return $obj;
    }
}

<?php


namespace Espo\Core\Acl;

use stdClass;
use InvalidArgumentException;
use RuntimeException;


class ScopeData
{
    
    private $raw;
    
    private $actionData = [];
    private bool $isBoolean = false;

    private function __construct() {}

    
    public function __get(string $name)
    {
        throw new RuntimeException("Accessing ScopeData properties is not allowed.");
    }

    
    public function isBoolean(): bool
    {
        return $this->isBoolean;
    }

    
    public function isTrue(): bool
    {
        if (!$this->isBoolean) {
            return false;
        }

        return $this->raw === true;
    }

    
    public function isFalse(): bool
    {
        if (!$this->isBoolean) {
            return false;
        }

        return $this->raw === false;
    }

    
    public function hasNotNo(): bool
    {
        foreach ($this->actionData as $level) {
            if ($level !== Table::LEVEL_NO) {
                return true;
            }
        }

        return false;
    }

    
    public function get(string $action): string
    {
        return $this->actionData[$action] ?? Table::LEVEL_NO;
    }

    
    public function getRead(): string
    {
        return $this->get(Table::ACTION_READ);
    }

    
    public function getStream(): string
    {
        return $this->get(Table::ACTION_STREAM);
    }

    
    public function getCreate(): string
    {
        return $this->get(Table::ACTION_CREATE);
    }

    
    public function getEdit(): string
    {
        return $this->get(Table::ACTION_EDIT);
    }

    
    public function getDelete(): string
    {
        return $this->get(Table::ACTION_DELETE);
    }

    
    public static function fromRaw($raw): self
    {
        

        $obj = new self();

        if ($raw instanceof stdClass) {
            $obj->isBoolean = false;

            $obj->actionData = get_object_vars($raw);

            foreach ($obj->actionData as $item) {
                if (!is_string($item)) {
                    throw new RuntimeException("Bad raw scope data.");
                }
            }
        }
        else if (is_bool($raw)) {
            $obj->isBoolean = true;
        }
        else {
            throw new InvalidArgumentException();
        }

        $obj->raw = $raw;

        return $obj;
    }
}

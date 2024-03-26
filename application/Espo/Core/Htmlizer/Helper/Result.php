<?php


namespace Espo\Core\Htmlizer\Helper;

class Result
{
    
    private $value = null;

    private function __construct() {}

    public static function createSafeString(string $value): self
    {
        $obj = new self();
        $obj->value = new SafeString($value);

        return $obj;
    }

    public static function createEmpty(): self
    {
        $obj = new self();
        $obj->value = '';

        return $obj;
    }

    public static function create(string $value): self
    {
        $obj = new self();
        $obj->value = $value;

        return $obj;
    }

    
    public function getValue()
    {
        if ($this->value instanceof SafeString) {
            return $this->value;
        }

        return (string) $this->value;
    }
}

<?php


namespace Espo\Core\Binding;

use LogicException;

class Binding
{
    public const IMPLEMENTATION_CLASS_NAME = 1;
    public const CONTAINER_SERVICE = 2;
    public const VALUE = 3;
    public const CALLBACK = 4;
    public const FACTORY_CLASS_NAME = 5;

    private int $type;
    
    private $value;

    
    private function __construct(int $type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function getType(): int
    {
        return $this->type;
    }

    
    public function getValue()
    {
        return $this->value;
    }

    
    public static function createFromImplementationClassName(string $implementationClassName): self
    {
        if (!$implementationClassName) {
            throw new LogicException("Bad binding.");
        }

        return new self(self::IMPLEMENTATION_CLASS_NAME, $implementationClassName);
    }

    public static function createFromServiceName(string $serviceName): self
    {
        if (!$serviceName) {
            throw new LogicException("Bad binding.");
        }

        return new self(self::CONTAINER_SERVICE, $serviceName);
    }

    
    public static function createFromValue($value): self
    {
        return new self(self::VALUE, $value);
    }

    public static function createFromCallback(callable $callback): self
    {
        return new self(self::CALLBACK, $callback);
    }

    
    public static function createFromFactoryClassName(string $factoryClassName): self
    {
        if (!$factoryClassName) {
            throw new LogicException("Bad binding.");
        }

        return new self(self::FACTORY_CLASS_NAME, $factoryClassName);
    }
}

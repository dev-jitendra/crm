<?php

declare(strict_types=1);



namespace Carbon\PHPStan;

use Closure;
use PHPStan\Reflection\Php\BuiltinMethodReflection;
use PHPStan\TrinaryLogic;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionType;
use stdClass;
use Throwable;

abstract class AbstractMacro implements BuiltinMethodReflection
{
    
    protected $reflectionFunction;

    
    private $className;

    
    private $methodName;

    
    private $parameters;

    
    private $static = false;

    
    public function __construct(string $className, string $methodName, $macro)
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->reflectionFunction = \is_array($macro)
            ? new ReflectionMethod($macro[0], $macro[1])
            : new ReflectionFunction($macro);
        $this->parameters = $this->reflectionFunction->getParameters();

        if ($this->reflectionFunction->isClosure()) {
            try {
                $closure = $this->reflectionFunction->getClosure();
                $boundClosure = Closure::bind($closure, new stdClass());
                $this->static = (!$boundClosure || (new ReflectionFunction($boundClosure))->getClosureThis() === null);
            } catch (Throwable $e) {
                $this->static = true;
            }
        }
    }

    
    public function getDeclaringClass(): ReflectionClass
    {
        return new ReflectionClass($this->className);
    }

    
    public function isPrivate(): bool
    {
        return false;
    }

    
    public function isPublic(): bool
    {
        return true;
    }

    
    public function isFinal(): bool
    {
        return false;
    }

    
    public function isInternal(): bool
    {
        return false;
    }

    
    public function isAbstract(): bool
    {
        return false;
    }

    
    public function isStatic(): bool
    {
        return $this->static;
    }

    
    public function getDocComment(): ?string
    {
        return $this->reflectionFunction->getDocComment() ?: null;
    }

    
    public function getName(): string
    {
        return $this->methodName;
    }

    
    public function getParameters(): array
    {
        return $this->parameters;
    }

    
    public function getReturnType(): ?ReflectionType
    {
        return $this->reflectionFunction->getReturnType();
    }

    
    public function isDeprecated(): TrinaryLogic
    {
        return TrinaryLogic::createFromBoolean(
            $this->reflectionFunction->isDeprecated() ||
            preg_match('/@deprecated/i', $this->getDocComment() ?: '')
        );
    }

    
    public function isVariadic(): bool
    {
        return $this->reflectionFunction->isVariadic();
    }

    
    public function getPrototype(): BuiltinMethodReflection
    {
        return $this;
    }

    public function getTentativeReturnType(): ?ReflectionType
    {
        return null;
    }
}

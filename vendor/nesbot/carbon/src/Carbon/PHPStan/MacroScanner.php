<?php



namespace Carbon\PHPStan;

use Carbon\CarbonInterface;
use ReflectionClass;
use ReflectionException;

final class MacroScanner
{
    
    public function hasMethod(string $className, string $methodName): bool
    {
        return is_a($className, CarbonInterface::class, true) &&
            \is_callable([$className, 'hasMacro']) &&
            $className::hasMacro($methodName);
    }

    
    public function getMethod(string $className, string $methodName): Macro
    {
        $reflectionClass = new ReflectionClass($className);
        $property = $reflectionClass->getProperty('globalMacros');

        $property->setAccessible(true);
        $macro = $property->getValue()[$methodName];

        return new Macro(
            $className,
            $methodName,
            $macro
        );
    }
}

<?php

declare(strict_types=1);



namespace Carbon\PHPStan;

use PHPStan\BetterReflection\Reflection;
use ReflectionMethod;

if (!class_exists(AbstractReflectionMacro::class, false)) {
    abstract class AbstractReflectionMacro extends AbstractMacro
    {
        
        public function getReflection(): ?Reflection\Adapter\ReflectionMethod
        {
            if ($this->reflectionFunction instanceof Reflection\Adapter\ReflectionMethod) {
                return $this->reflectionFunction;
            }

            return $this->reflectionFunction instanceof ReflectionMethod
                ? new Reflection\Adapter\ReflectionMethod(
                    Reflection\ReflectionMethod::createFromName(
                        $this->reflectionFunction->getDeclaringClass()->getName(),
                        $this->reflectionFunction->getName()
                    )
                )
                : null;
        }
    }
}

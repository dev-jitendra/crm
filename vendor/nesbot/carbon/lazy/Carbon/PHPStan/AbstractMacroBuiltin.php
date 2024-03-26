<?php

declare(strict_types=1);



namespace Carbon\PHPStan;

use ReflectionMethod;

if (!class_exists(AbstractReflectionMacro::class, false)) {
    abstract class AbstractReflectionMacro extends AbstractMacro
    {
        
        public function getReflection(): ?ReflectionMethod
        {
            return $this->reflectionFunction instanceof ReflectionMethod
                ? $this->reflectionFunction
                : null;
        }
    }
}

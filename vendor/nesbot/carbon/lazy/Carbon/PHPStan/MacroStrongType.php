<?php

declare(strict_types=1);



namespace Carbon\PHPStan;

if (!class_exists(LazyMacro::class, false)) {
    abstract class LazyMacro extends AbstractReflectionMacro
    {
        
        public function getFileName(): ?string
        {
            return $this->reflectionFunction->getFileName();
        }

        
        public function getStartLine(): ?int
        {
            return $this->reflectionFunction->getStartLine();
        }

        
        public function getEndLine(): ?int
        {
            return $this->reflectionFunction->getEndLine();
        }
    }
}

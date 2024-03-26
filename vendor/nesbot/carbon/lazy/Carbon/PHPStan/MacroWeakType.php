<?php

declare(strict_types=1);



namespace Carbon\PHPStan;

if (!class_exists(LazyMacro::class, false)) {
    abstract class LazyMacro extends AbstractReflectionMacro
    {
        
        public function getFileName()
        {
            return $this->reflectionFunction->getFileName();
        }

        
        public function getStartLine()
        {
            return $this->reflectionFunction->getStartLine();
        }

        
        public function getEndLine()
        {
            return $this->reflectionFunction->getEndLine();
        }
    }
}

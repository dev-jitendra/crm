<?php


namespace Espo\Core\FileStorage;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

use RuntimeException;

class Factory
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    public function create(string $name): Storage
    {
        
        $className = $this->metadata->get(['app', 'fileStorage', 'implementationClassNameMap', $name]);

        if (!$className) {
            throw new RuntimeException("Unknown file storage '{$name}'.");
        }

        return $this->injectableFactory->create($className);
    }
}

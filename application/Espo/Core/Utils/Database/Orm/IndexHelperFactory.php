<?php


namespace Espo\Core\Utils\Database\Orm;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use RuntimeException;

class IndexHelperFactory
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    public function create(string $platform): IndexHelper
    {
        
        $className = $this->metadata
            ->get(['app', 'databasePlatforms', $platform, 'indexHelperClassName']);

        if (!$className) {
            throw new RuntimeException("No Index Helper for {$platform}");
        }

        return $this->injectableFactory->create($className);
    }
}

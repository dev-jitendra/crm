<?php


namespace Espo\Core\Utils\Database\Schema;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Database\Helper;
use Espo\Core\Utils\Metadata;
use RuntimeException;

class ColumnPreparatorFactory
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory,
        private Helper $helper
    ) {}

    public function create(string $platform): ColumnPreparator
    {
        
        $className = $this->metadata
            ->get(['app', 'databasePlatforms', $platform, 'columnPreparatorClassName']);

        if (!$className) {
            throw new RuntimeException("No Column-Preparator for {$platform}.");
        }

        $binding = BindingContainerBuilder::create()
            ->bindInstance(Helper::class, $this->helper)
            ->build();

        return $this->injectableFactory->createWithBinding($className, $binding);
    }
}

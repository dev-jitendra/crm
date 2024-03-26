<?php


namespace Espo\Core\Utils\Database;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use PDO;
use RuntimeException;

class DetailsProviderFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata
    ) {}

    public function create(string $platform, PDO $pdo): DetailsProvider
    {
        
        $className = $this->metadata
            ->get(['app', 'databasePlatforms', $platform, 'detailsProviderClassName']);

        if (!$className) {
            throw new RuntimeException("No Details-Provider for {$platform}.");
        }

        $binding = BindingContainerBuilder::create()
            ->bindInstance(PDO::class, $pdo)
            ->build();

        return $this->injectableFactory->createWithBinding($className, $binding);
    }
}

<?php


namespace Espo\Core\Utils\Database\Dbal;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use PDO;
use RuntimeException;

class ConnectionFactoryFactory
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    public function create(string $platform, PDO $pdo): ConnectionFactory
    {
        
        $className = $this->metadata
            ->get(['app', 'databasePlatforms', $platform, 'dbalConnectionFactoryClassName']);

        if (!$className) {
            throw new RuntimeException("No DBAL ConnectionFactory for {$platform}.");
        }

        $bindingContainer = BindingContainerBuilder::create()
            ->bindInstance(PDO::class, $pdo)
            ->build();

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }
}

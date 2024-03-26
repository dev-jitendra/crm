<?php


namespace Espo\Core\ORM;

use Espo\Core\ORM\PDO\PDOFactoryFactory;
use Espo\Core\ORM\QueryComposer\QueryComposerFactory;
use Espo\Core\InjectableFactory;
use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\ORM\QueryComposer\Part\FunctionConverterFactory;

use Espo\Core\Utils\Log;
use Espo\ORM\Executor\DefaultSqlExecutor;
use Espo\ORM\Metadata;
use Espo\ORM\EventDispatcher;
use Espo\ORM\DatabaseParams;
use Espo\ORM\PDO\PDOFactory;
use Espo\ORM\QueryComposer\QueryComposerFactory as QueryComposerFactoryInterface;
use Espo\ORM\Repository\RepositoryFactory as RepositoryFactoryInterface;
use Espo\ORM\EntityFactory as EntityFactoryInterface;
use Espo\ORM\Executor\SqlExecutor;
use Espo\ORM\Value\ValueFactoryFactory as ValueFactoryFactoryInterface;
use Espo\ORM\Value\AttributeExtractorFactory as AttributeExtractorFactoryInterface;
use Espo\ORM\PDO\PDOProvider;
use Espo\ORM\QueryComposer\Part\FunctionConverterFactory as FunctionConverterFactoryInterface;

use RuntimeException;

class EntityManagerFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private MetadataDataProvider $metadataDataProvider,
        private EventDispatcher $eventDispatcher,
        private PDOFactoryFactory $pdoFactoryFactory,
        private DatabaseParamsFactory $databaseParamsFactory,
        private ConfigDataProvider $configDataProvider,
        private Log $log
    ) {}

    public function create(): EntityManager
    {
        $entityFactory = $this->injectableFactory->create(EntityFactory::class);

        $repositoryFactory = $this->injectableFactory->createWithBinding(
            RepositoryFactory::class,
            BindingContainerBuilder::create()
                ->bindInstance(EntityFactoryInterface::class, $entityFactory)
                ->build()
        );

        $databaseParams = $this->createDatabaseParams();

        $metadata = new Metadata($this->metadataDataProvider, $this->eventDispatcher);

        $valueFactoryFactory = $this->injectableFactory->createWithBinding(
            ValueFactoryFactory::class,
            BindingContainerBuilder::create()
                ->bindInstance(Metadata::class, $metadata)
                ->build()
        );

        $attributeExtractorFactory = $this->injectableFactory->createWithBinding(
            AttributeExtractorFactory::class,
            BindingContainerBuilder::create()
                ->bindInstance(Metadata::class, $metadata)
                ->build()
        );

        $functionConverterFactory = $this->injectableFactory->createWithBinding(
            FunctionConverterFactory::class,
            BindingContainerBuilder::create()
                ->bindInstance(DatabaseParams::class, $databaseParams)
                ->build()
        );

        $pdoFactory = $this->pdoFactoryFactory->create($databaseParams->getPlatform() ?? '');

        $pdoProvider = $this->injectableFactory->createResolved(
            PDOProvider::class,
            BindingContainerBuilder::create()
                ->bindInstance(DatabaseParams::class, $databaseParams)
                ->bindInstance(PDOFactory::class, $pdoFactory)
                ->build()
        );

        $queryComposerFactory = $this->injectableFactory->createWithBinding(
            QueryComposerFactory::class,
            BindingContainerBuilder::create()
                ->bindInstance(PDOProvider::class, $pdoProvider)
                ->bindInstance(Metadata::class, $metadata)
                ->bindInstance(EntityFactoryInterface::class, $entityFactory)
                ->bindInstance(FunctionConverterFactoryInterface::class, $functionConverterFactory)
                ->build()
        );

        $sqlExecutor = new DefaultSqlExecutor($pdoProvider, $this->log, $this->configDataProvider->logSql());

        $binding = BindingContainerBuilder::create()
            ->bindInstance(DatabaseParams::class, $databaseParams)
            ->bindInstance(Metadata::class, $metadata)
            ->bindInstance(QueryComposerFactoryInterface::class, $queryComposerFactory)
            ->bindInstance(RepositoryFactoryInterface::class, $repositoryFactory)
            ->bindInstance(EntityFactoryInterface::class, $entityFactory)
            ->bindInstance(ValueFactoryFactoryInterface::class, $valueFactoryFactory)
            ->bindInstance(AttributeExtractorFactoryInterface::class, $attributeExtractorFactory)
            ->bindInstance(EventDispatcher::class, $this->eventDispatcher)
            ->bindInstance(PDOProvider::class, $pdoProvider)
            ->bindInstance(FunctionConverterFactoryInterface::class, $functionConverterFactory)
            ->bindInstance(SqlExecutor::class, $sqlExecutor)
            ->build();

        return $this->injectableFactory->createWithBinding(EntityManager::class, $binding);
    }

    private function createDatabaseParams(): DatabaseParams
    {
        $databaseParams = $this->databaseParamsFactory->create();

        if (!$databaseParams->getName()) {
            throw new RuntimeException('No database name specified in config.');
        }

        return $databaseParams;
    }
}

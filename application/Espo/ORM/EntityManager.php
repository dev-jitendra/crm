<?php


namespace Espo\ORM;

use Espo\ORM\Defs\Defs;
use Espo\ORM\Executor\DefaultQueryExecutor;
use Espo\ORM\Executor\DefaultSqlExecutor;
use Espo\ORM\Executor\QueryExecutor;
use Espo\ORM\Executor\SqlExecutor;
use Espo\ORM\QueryComposer\QueryComposer;
use Espo\ORM\QueryComposer\QueryComposerFactory;
use Espo\ORM\QueryComposer\QueryComposerWrapper;
use Espo\ORM\Mapper\Mapper;
use Espo\ORM\Mapper\MapperFactory;
use Espo\ORM\Mapper\BaseMapper;
use Espo\ORM\Repository\RepositoryFactory;
use Espo\ORM\Repository\Repository;
use Espo\ORM\Repository\RDBRepository;
use Espo\ORM\Repository\Util as RepositoryUtil;
use Espo\ORM\Locker\Locker;
use Espo\ORM\Locker\BaseLocker;
use Espo\ORM\Locker\MysqlLocker;
use Espo\ORM\Value\ValueAccessorFactory;
use Espo\ORM\Value\ValueFactoryFactory;
use Espo\ORM\Value\AttributeExtractorFactory;
use Espo\ORM\PDO\PDOProvider;

use PDO;
use RuntimeException;
use stdClass;


class EntityManager
{
    private CollectionFactory $collectionFactory;
    private QueryComposer $queryComposer;
    private QueryExecutor $queryExecutor;
    private QueryBuilder $queryBuilder;
    private SqlExecutor $sqlExecutor;
    private TransactionManager $transactionManager;
    private Locker $locker;

    private const RDB_MAPPER_NAME = 'RDB';

    
    private $repositoryHash = [];
    
    private $mappers = [];

    
    public function __construct(
        private DatabaseParams $databaseParams,
        private Metadata $metadata,
        private RepositoryFactory $repositoryFactory,
        private EntityFactory $entityFactory,
        private QueryComposerFactory $queryComposerFactory,
        ValueFactoryFactory $valueFactoryFactory,
        AttributeExtractorFactory $attributeExtractorFactory,
        EventDispatcher $eventDispatcher,
        private PDOProvider $pdoProvider,
        private ?MapperFactory $mapperFactory = null,
        ?QueryExecutor $queryExecutor = null,
        ?SqlExecutor $sqlExecutor = null
    ) {
        if (!$this->databaseParams->getPlatform()) {
            throw new RuntimeException("No 'platform' parameter.");
        }

        $valueAccessorFactory = new ValueAccessorFactory(
            $valueFactoryFactory,
            $attributeExtractorFactory,
            $eventDispatcher
        );

        $this->entityFactory->setEntityManager($this);
        $this->entityFactory->setValueAccessorFactory($valueAccessorFactory);

        $this->initQueryComposer();

        $this->sqlExecutor = $sqlExecutor ?? new DefaultSqlExecutor($this->pdoProvider);
        $this->queryExecutor = $queryExecutor ??
            new DefaultQueryExecutor($this->sqlExecutor, $this->getQueryComposer());
        $this->queryBuilder = new QueryBuilder();
        $this->collectionFactory = new CollectionFactory($this);
        $this->transactionManager = new TransactionManager($this->pdoProvider->get(), $this->queryComposer);

        $this->initLocker();
    }

    private function initQueryComposer(): void
    {
        $platform = $this->databaseParams->getPlatform() ?? '';

        $this->queryComposer = $this->queryComposerFactory->create($platform);
    }

    private function initLocker(): void
    {
        $platform = $this->databaseParams->getPlatform() ?? '';

        $className = BaseLocker::class;

        if ($platform === 'Mysql') {
            $className = MysqlLocker::class;
        }

        $this->locker = new $className($this->pdoProvider->get(), $this->queryComposer, $this->transactionManager);
    }

    
    public function getQueryComposer(): QueryComposerWrapper
    {
        return new QueryComposerWrapper($this->queryComposer);
    }

    
    public function getTransactionManager(): TransactionManager
    {
        return $this->transactionManager;
    }

    
    public function getLocker(): Locker
    {
        return $this->locker;
    }

    
    public function getMapper(string $name = self::RDB_MAPPER_NAME): Mapper
    {
        if (!array_key_exists($name, $this->mappers)) {
            $this->loadMapper($name);
        }

        return $this->mappers[$name];
    }

    private function loadMapper(string $name): void
    {
        if ($name === self::RDB_MAPPER_NAME) {
            $mapper = new BaseMapper(
                $this->pdoProvider->get(),
                $this->entityFactory,
                $this->collectionFactory,
                $this->metadata,
                $this->queryExecutor
            );

            $this->mappers[$name] = $mapper;

            return;
        }

        if (!$this->mapperFactory) {
            throw new RuntimeException("Could not create mapper '$name'. No mapper factory.");
        }

        $this->mappers[$name] = $this->mapperFactory->create($name);
    }

    
    public function getEntity(string $entityType, ?string $id = null): ?Entity
    {
        if (!$this->hasRepository($entityType)) {
            throw new RuntimeException("ORM: Repository '$entityType' does not exist.");
        }

        if ($id === null) {
            return $this->getRepository($entityType)->getNew();
        }

        return $this->getRepository($entityType)->getById($id);
    }

    
    public function getNewEntity(string $entityType): Entity
    {
        
        return $this->getEntity($entityType);
    }

    
    public function getEntityById(string $entityType, string $id): ?Entity
    {
        return $this->getEntity($entityType, $id);
    }

    
    public function saveEntity(Entity $entity, array $options = []): void
    {
        $entityType = $entity->getEntityType();

        $this->getRepository($entityType)->save($entity, $options);
    }

    
    public function removeEntity(Entity $entity, array $options = []): void
    {
        $entityType = $entity->getEntityType();

        $this->getRepository($entityType)->remove($entity, $options);
    }

    
    public function refreshEntity(Entity $entity): void
    {
        if ($entity->isNew()) {
            throw new RuntimeException("Can't refresh a new entity.");
        }

        if (!$entity->hasId()) {
            throw new RuntimeException("Can't refresh an entity w/o ID.");
        }

        $fetchedEntity = $this->getEntityById($entity->getEntityType(), $entity->getId());

        if (!$fetchedEntity) {
            throw new RuntimeException("Can't refresh a non-existent entity.");
        }

        $entity->set($fetchedEntity->getValueMap());
        $entity->setAsFetched();
    }

    
    public function createEntity(string $entityType, $data = [], array $options = []): Entity
    {
        $entity = $this->getNewEntity($entityType);
        $entity->set($data);
        $this->saveEntity($entity, $options);

        return $entity;
    }

    
    public function hasRepository(string $entityType): bool
    {
        return $this->getMetadata()->has($entityType);
    }

    
    public function getRepository(string $entityType): Repository
    {
        if (!$this->hasRepository($entityType)) {
            throw new RuntimeException("Repository '$entityType' does not exist.");
        }

        if (!array_key_exists($entityType, $this->repositoryHash)) {
            $this->repositoryHash[$entityType] = $this->repositoryFactory->create($entityType);
        }

        return $this->repositoryHash[$entityType];
    }

    
    public function getRDBRepository(string $entityType): RDBRepository
    {
        $repository = $this->getRepository($entityType);

        if (!$repository instanceof RDBRepository) {
            throw new RuntimeException("Repository '$entityType' is not RDB.");
        }

        return $repository;
    }

    
    public function getRDBRepositoryByClass(string $className): RDBRepository
    {
        $entityType = RepositoryUtil::getEntityTypeByClass($className);

        
        return $this->getRDBRepository($entityType);
    }

    
    public function getRepositoryByClass(string $className): Repository
    {
        $entityType = RepositoryUtil::getEntityTypeByClass($className);

        
        return $this->getRepository($entityType);
    }

    
    public function getDefs(): Defs
    {
        return $this->metadata->getDefs();
    }

    
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    
    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    
    public function getEntityFactory(): EntityFactory
    {
        return $this->entityFactory;
    }

    
    public function getCollectionFactory(): CollectionFactory
    {
        return $this->collectionFactory;
    }

    
    public function getQueryExecutor(): QueryExecutor
    {
        return $this->queryExecutor;
    }

    
    public function getSqlExecutor(): SqlExecutor
    {
        return $this->sqlExecutor;
    }

    
    public function createCollection(?string $entityType = null, array $data = []): EntityCollection
    {
        return $this->collectionFactory->create($entityType, $data);
    }

    
    public function getPDO(): PDO
    {
        return $this->pdoProvider->get();
    }

    
    public function getQuery(): QueryComposer
    {
        return $this->queryComposer;
    }
}

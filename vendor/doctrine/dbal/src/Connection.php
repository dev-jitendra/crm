<?php

namespace Doctrine\DBAL;

use Closure;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Cache\ArrayResult;
use Doctrine\DBAL\Cache\CacheException;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use Doctrine\DBAL\Driver\Statement as DriverStatement;
use Doctrine\DBAL\Event\TransactionBeginEventArgs;
use Doctrine\DBAL\Event\TransactionCommitEventArgs;
use Doctrine\DBAL\Event\TransactionRollBackEventArgs;
use Doctrine\DBAL\Exception\ConnectionLost;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\SQL\Parser;
use Doctrine\DBAL\Types\Type;
use Doctrine\Deprecations\Deprecation;
use LogicException;
use Throwable;
use Traversable;

use function array_key_exists;
use function assert;
use function count;
use function get_class;
use function implode;
use function is_int;
use function is_string;
use function key;
use function method_exists;
use function sprintf;


class Connection
{
    
    public const PARAM_INT_ARRAY = ParameterType::INTEGER + self::ARRAY_PARAM_OFFSET;

    
    public const PARAM_STR_ARRAY = ParameterType::STRING + self::ARRAY_PARAM_OFFSET;

    
    public const PARAM_ASCII_STR_ARRAY = ParameterType::ASCII + self::ARRAY_PARAM_OFFSET;

    
    public const ARRAY_PARAM_OFFSET = 100;

    
    protected $_conn;

    
    protected $_config;

    
    protected $_eventManager;

    
    protected $_expr;

    
    private bool $autoCommit = true;

    
    private int $transactionNestingLevel = 0;

    
    private $transactionIsolationLevel;

    
    private bool $nestTransactionsWithSavepoints = false;

    
    private array $params;

    
    private ?AbstractPlatform $platform = null;

    private ?ExceptionConverter $exceptionConverter = null;
    private ?Parser $parser                         = null;

    
    protected $_schemaManager;

    
    protected $_driver;

    
    private bool $isRollbackOnly = false;

    
    public function __construct(
        array $params,
        Driver $driver,
        ?Configuration $config = null,
        ?EventManager $eventManager = null
    ) {
        $this->_driver = $driver;
        $this->params  = $params;

        
        $config       ??= new Configuration();
        $eventManager ??= new EventManager();

        $this->_config       = $config;
        $this->_eventManager = $eventManager;

        if (isset($params['platform'])) {
            if (! $params['platform'] instanceof Platforms\AbstractPlatform) {
                throw Exception::invalidPlatformType($params['platform']);
            }

            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'The "platform" connection parameter is deprecated.'
                    . ' Use a driver middleware that would instantiate the platform instead.',
            );

            $this->platform = $params['platform'];
            $this->platform->setEventManager($this->_eventManager);
        }

        $this->_expr = $this->createExpressionBuilder();

        $this->autoCommit = $config->getAutoCommit();
    }

    
    public function getParams()
    {
        return $this->params;
    }

    
    public function getDatabase()
    {
        $platform = $this->getDatabasePlatform();
        $query    = $platform->getDummySelectSQL($platform->getCurrentDatabaseExpression());
        $database = $this->fetchOne($query);

        assert(is_string($database) || $database === null);

        return $database;
    }

    
    public function getDriver()
    {
        return $this->_driver;
    }

    
    public function getConfiguration()
    {
        return $this->_config;
    }

    
    public function getEventManager()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            '%s is deprecated.',
            __METHOD__,
        );

        return $this->_eventManager;
    }

    
    public function getDatabasePlatform()
    {
        if ($this->platform === null) {
            $this->platform = $this->detectDatabasePlatform();
            $this->platform->setEventManager($this->_eventManager);
        }

        return $this->platform;
    }

    
    public function createExpressionBuilder(): ExpressionBuilder
    {
        return new ExpressionBuilder($this);
    }

    
    public function getExpressionBuilder()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'Connection::getExpressionBuilder() is deprecated,'
                . ' use Connection::createExpressionBuilder() instead.',
        );

        return $this->_expr;
    }

    
    public function connect()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'Public access to Connection::connect() is deprecated.',
        );

        if ($this->_conn !== null) {
            return false;
        }

        try {
            $this->_conn = $this->_driver->connect($this->params);
        } catch (Driver\Exception $e) {
            throw $this->convertException($e);
        }

        if ($this->autoCommit === false) {
            $this->beginTransaction();
        }

        if ($this->_eventManager->hasListeners(Events::postConnect)) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Subscribing to %s events is deprecated. Implement a middleware instead.',
                Events::postConnect,
            );

            $eventArgs = new Event\ConnectionEventArgs($this);
            $this->_eventManager->dispatchEvent(Events::postConnect, $eventArgs);
        }

        return true;
    }

    
    private function detectDatabasePlatform(): AbstractPlatform
    {
        $version = $this->getDatabasePlatformVersion();

        if ($version !== null) {
            assert($this->_driver instanceof VersionAwarePlatformDriver);

            return $this->_driver->createDatabasePlatformForVersion($version);
        }

        return $this->_driver->getDatabasePlatform();
    }

    
    private function getDatabasePlatformVersion()
    {
        
        if (! $this->_driver instanceof VersionAwarePlatformDriver) {
            return null;
        }

        
        if (isset($this->params['serverVersion'])) {
            return $this->params['serverVersion'];
        }

        
        if ($this->_conn === null) {
            try {
                $this->connect();
            } catch (Exception $originalException) {
                if (! isset($this->params['dbname'])) {
                    throw $originalException;
                }

                Deprecation::trigger(
                    'doctrine/dbal',
                    'https:
                    'Relying on a fallback connection used to determine the database platform while connecting'
                        . ' to a non-existing database is deprecated. Either use an existing database name in'
                        . ' connection parameters or omit the database name if the platform'
                        . ' and the server configuration allow that.',
                );

                
                
                $params = $this->params;

                unset($this->params['dbname']);

                try {
                    $this->connect();
                } catch (Exception $fallbackException) {
                    
                    
                    throw $originalException;
                } finally {
                    $this->params = $params;
                }

                $serverVersion = $this->getServerVersion();

                
                $this->close();

                return $serverVersion;
            }
        }

        return $this->getServerVersion();
    }

    
    private function getServerVersion()
    {
        $connection = $this->getWrappedConnection();

        
        if ($connection instanceof ServerInfoAwareConnection) {
            try {
                return $connection->getServerVersion();
            } catch (Driver\Exception $e) {
                throw $this->convertException($e);
            }
        }

        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'Not implementing the ServerInfoAwareConnection interface in %s is deprecated',
            get_class($connection),
        );

        
        return null;
    }

    
    public function isAutoCommit()
    {
        return $this->autoCommit === true;
    }

    
    public function setAutoCommit($autoCommit)
    {
        $autoCommit = (bool) $autoCommit;

        
        if ($autoCommit === $this->autoCommit) {
            return;
        }

        $this->autoCommit = $autoCommit;

        
        if ($this->_conn === null || $this->transactionNestingLevel === 0) {
            return;
        }

        $this->commitAll();
    }

    
    public function fetchAssociative(string $query, array $params = [], array $types = [])
    {
        return $this->executeQuery($query, $params, $types)->fetchAssociative();
    }

    
    public function fetchNumeric(string $query, array $params = [], array $types = [])
    {
        return $this->executeQuery($query, $params, $types)->fetchNumeric();
    }

    
    public function fetchOne(string $query, array $params = [], array $types = [])
    {
        return $this->executeQuery($query, $params, $types)->fetchOne();
    }

    
    public function isConnected()
    {
        return $this->_conn !== null;
    }

    
    public function isTransactionActive()
    {
        return $this->transactionNestingLevel > 0;
    }

    
    private function addCriteriaCondition(
        array $criteria,
        array &$columns,
        array &$values,
        array &$conditions
    ): void {
        $platform = $this->getDatabasePlatform();

        foreach ($criteria as $columnName => $value) {
            if ($value === null) {
                $conditions[] = $platform->getIsNullExpression($columnName);
                continue;
            }

            $columns[]    = $columnName;
            $values[]     = $value;
            $conditions[] = $columnName . ' = ?';
        }
    }

    
    public function delete($table, array $criteria, array $types = [])
    {
        if (count($criteria) === 0) {
            throw InvalidArgumentException::fromEmptyCriteria();
        }

        $columns = $values = $conditions = [];

        $this->addCriteriaCondition($criteria, $columns, $values, $conditions);

        return $this->executeStatement(
            'DELETE FROM ' . $table . ' WHERE ' . implode(' AND ', $conditions),
            $values,
            is_string(key($types)) ? $this->extractTypeValues($columns, $types) : $types,
        );
    }

    
    public function close()
    {
        $this->_conn                   = null;
        $this->transactionNestingLevel = 0;
    }

    
    public function setTransactionIsolation($level)
    {
        $this->transactionIsolationLevel = $level;

        return $this->executeStatement($this->getDatabasePlatform()->getSetTransactionIsolationSQL($level));
    }

    
    public function getTransactionIsolation()
    {
        return $this->transactionIsolationLevel ??= $this->getDatabasePlatform()->getDefaultTransactionIsolationLevel();
    }

    
    public function update($table, array $data, array $criteria, array $types = [])
    {
        $columns = $values = $conditions = $set = [];

        foreach ($data as $columnName => $value) {
            $columns[] = $columnName;
            $values[]  = $value;
            $set[]     = $columnName . ' = ?';
        }

        $this->addCriteriaCondition($criteria, $columns, $values, $conditions);

        if (is_string(key($types))) {
            $types = $this->extractTypeValues($columns, $types);
        }

        $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $set)
                . ' WHERE ' . implode(' AND ', $conditions);

        return $this->executeStatement($sql, $values, $types);
    }

    
    public function insert($table, array $data, array $types = [])
    {
        if (count($data) === 0) {
            return $this->executeStatement('INSERT INTO ' . $table . ' () VALUES ()');
        }

        $columns = [];
        $values  = [];
        $set     = [];

        foreach ($data as $columnName => $value) {
            $columns[] = $columnName;
            $values[]  = $value;
            $set[]     = '?';
        }

        return $this->executeStatement(
            'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ')' .
            ' VALUES (' . implode(', ', $set) . ')',
            $values,
            is_string(key($types)) ? $this->extractTypeValues($columns, $types) : $types,
        );
    }

    
    private function extractTypeValues(array $columnList, array $types): array
    {
        $typeValues = [];

        foreach ($columnList as $columnName) {
            $typeValues[] = $types[$columnName] ?? ParameterType::STRING;
        }

        return $typeValues;
    }

    
    public function quoteIdentifier($str)
    {
        return $this->getDatabasePlatform()->quoteIdentifier($str);
    }

    
    public function quote($value, $type = ParameterType::STRING)
    {
        $connection = $this->getWrappedConnection();

        [$value, $bindingType] = $this->getBindingInfo($value, $type);

        return $connection->quote($value, $bindingType);
    }

    
    public function fetchAllNumeric(string $query, array $params = [], array $types = []): array
    {
        return $this->executeQuery($query, $params, $types)->fetchAllNumeric();
    }

    
    public function fetchAllAssociative(string $query, array $params = [], array $types = []): array
    {
        return $this->executeQuery($query, $params, $types)->fetchAllAssociative();
    }

    
    public function fetchAllKeyValue(string $query, array $params = [], array $types = []): array
    {
        return $this->executeQuery($query, $params, $types)->fetchAllKeyValue();
    }

    
    public function fetchAllAssociativeIndexed(string $query, array $params = [], array $types = []): array
    {
        return $this->executeQuery($query, $params, $types)->fetchAllAssociativeIndexed();
    }

    
    public function fetchFirstColumn(string $query, array $params = [], array $types = []): array
    {
        return $this->executeQuery($query, $params, $types)->fetchFirstColumn();
    }

    
    public function iterateNumeric(string $query, array $params = [], array $types = []): Traversable
    {
        return $this->executeQuery($query, $params, $types)->iterateNumeric();
    }

    
    public function iterateAssociative(string $query, array $params = [], array $types = []): Traversable
    {
        return $this->executeQuery($query, $params, $types)->iterateAssociative();
    }

    
    public function iterateKeyValue(string $query, array $params = [], array $types = []): Traversable
    {
        return $this->executeQuery($query, $params, $types)->iterateKeyValue();
    }

    
    public function iterateAssociativeIndexed(string $query, array $params = [], array $types = []): Traversable
    {
        return $this->executeQuery($query, $params, $types)->iterateAssociativeIndexed();
    }

    
    public function iterateColumn(string $query, array $params = [], array $types = []): Traversable
    {
        return $this->executeQuery($query, $params, $types)->iterateColumn();
    }

    
    public function prepare(string $sql): Statement
    {
        $connection = $this->getWrappedConnection();

        try {
            $statement = $connection->prepare($sql);
        } catch (Driver\Exception $e) {
            throw $this->convertExceptionDuringQuery($e, $sql);
        }

        return new Statement($this, $statement, $sql);
    }

    
    public function executeQuery(
        string $sql,
        array $params = [],
        $types = [],
        ?QueryCacheProfile $qcp = null
    ): Result {
        if ($qcp !== null) {
            return $this->executeCacheQuery($sql, $params, $types, $qcp);
        }

        $connection = $this->getWrappedConnection();

        $logger = $this->_config->getSQLLogger();
        if ($logger !== null) {
            $logger->startQuery($sql, $params, $types);
        }

        try {
            if (count($params) > 0) {
                if ($this->needsArrayParameterConversion($params, $types)) {
                    [$sql, $params, $types] = $this->expandArrayParameters($sql, $params, $types);
                }

                $stmt = $connection->prepare($sql);

                $this->bindParameters($stmt, $params, $types);

                $result = $stmt->execute();
            } else {
                $result = $connection->query($sql);
            }

            return new Result($result, $this);
        } catch (Driver\Exception $e) {
            throw $this->convertExceptionDuringQuery($e, $sql, $params, $types);
        } finally {
            if ($logger !== null) {
                $logger->stopQuery();
            }
        }
    }

    
    public function executeCacheQuery($sql, $params, $types, QueryCacheProfile $qcp): Result
    {
        $resultCache = $qcp->getResultCache() ?? $this->_config->getResultCache();

        if ($resultCache === null) {
            throw CacheException::noResultDriverConfigured();
        }

        $connectionParams = $this->params;
        unset($connectionParams['platform']);

        [$cacheKey, $realKey] = $qcp->generateCacheKeys($sql, $params, $types, $connectionParams);

        $item = $resultCache->getItem($cacheKey);

        if ($item->isHit()) {
            $value = $item->get();
            if (isset($value[$realKey])) {
                return new Result(new ArrayResult($value[$realKey]), $this);
            }
        } else {
            $value = [];
        }

        $data = $this->fetchAllAssociative($sql, $params, $types);

        $value[$realKey] = $data;

        $item->set($value);

        $lifetime = $qcp->getLifetime();
        if ($lifetime > 0) {
            $item->expiresAfter($lifetime);
        }

        $resultCache->save($item);

        return new Result(new ArrayResult($data), $this);
    }

    
    public function executeStatement($sql, array $params = [], array $types = [])
    {
        $connection = $this->getWrappedConnection();

        $logger = $this->_config->getSQLLogger();
        if ($logger !== null) {
            $logger->startQuery($sql, $params, $types);
        }

        try {
            if (count($params) > 0) {
                if ($this->needsArrayParameterConversion($params, $types)) {
                    [$sql, $params, $types] = $this->expandArrayParameters($sql, $params, $types);
                }

                $stmt = $connection->prepare($sql);

                $this->bindParameters($stmt, $params, $types);

                return $stmt->execute()
                    ->rowCount();
            }

            return $connection->exec($sql);
        } catch (Driver\Exception $e) {
            throw $this->convertExceptionDuringQuery($e, $sql, $params, $types);
        } finally {
            if ($logger !== null) {
                $logger->stopQuery();
            }
        }
    }

    
    public function getTransactionNestingLevel()
    {
        return $this->transactionNestingLevel;
    }

    
    public function lastInsertId($name = null)
    {
        if ($name !== null) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'The usage of Connection::lastInsertId() with a sequence name is deprecated.',
            );
        }

        try {
            return $this->getWrappedConnection()->lastInsertId($name);
        } catch (Driver\Exception $e) {
            throw $this->convertException($e);
        }
    }

    
    public function transactional(Closure $func)
    {
        $this->beginTransaction();
        try {
            $res = $func($this);
            $this->commit();

            return $res;
        } catch (Throwable $e) {
            $this->rollBack();

            throw $e;
        }
    }

    
    public function setNestTransactionsWithSavepoints($nestTransactionsWithSavepoints)
    {
        if (! $nestTransactionsWithSavepoints) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                <<<'DEPRECATION'
                Nesting transactions without enabling savepoints is deprecated.
                Call %s::setNestTransactionsWithSavepoints(true) to enable savepoints.
                DEPRECATION,
                self::class,
            );
        }

        if ($this->transactionNestingLevel > 0) {
            throw ConnectionException::mayNotAlterNestedTransactionWithSavepointsInTransaction();
        }

        if (! $this->getDatabasePlatform()->supportsSavepoints()) {
            throw ConnectionException::savepointsNotSupported();
        }

        $this->nestTransactionsWithSavepoints = (bool) $nestTransactionsWithSavepoints;
    }

    
    public function getNestTransactionsWithSavepoints()
    {
        return $this->nestTransactionsWithSavepoints;
    }

    
    protected function _getNestedTransactionSavePointName()
    {
        return 'DOCTRINE2_SAVEPOINT_' . $this->transactionNestingLevel;
    }

    
    public function beginTransaction()
    {
        $connection = $this->getWrappedConnection();

        ++$this->transactionNestingLevel;

        $logger = $this->_config->getSQLLogger();

        if ($this->transactionNestingLevel === 1) {
            if ($logger !== null) {
                $logger->startQuery('"START TRANSACTION"');
            }

            $connection->beginTransaction();

            if ($logger !== null) {
                $logger->stopQuery();
            }
        } elseif ($this->nestTransactionsWithSavepoints) {
            if ($logger !== null) {
                $logger->startQuery('"SAVEPOINT"');
            }

            $this->createSavepoint($this->_getNestedTransactionSavePointName());
            if ($logger !== null) {
                $logger->stopQuery();
            }
        } else {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                <<<'DEPRECATION'
                Nesting transactions without enabling savepoints is deprecated.
                Call %s::setNestTransactionsWithSavepoints(true) to enable savepoints.
                DEPRECATION,
                self::class,
            );
        }

        $eventManager = $this->getEventManager();

        if ($eventManager->hasListeners(Events::onTransactionBegin)) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Subscribing to %s events is deprecated.',
                Events::onTransactionBegin,
            );

            $eventManager->dispatchEvent(Events::onTransactionBegin, new TransactionBeginEventArgs($this));
        }

        return true;
    }

    
    public function commit()
    {
        if ($this->transactionNestingLevel === 0) {
            throw ConnectionException::noActiveTransaction();
        }

        if ($this->isRollbackOnly) {
            throw ConnectionException::commitFailedRollbackOnly();
        }

        $result = true;

        $connection = $this->getWrappedConnection();

        if ($this->transactionNestingLevel === 1) {
            $result = $this->doCommit($connection);
        } elseif ($this->nestTransactionsWithSavepoints) {
            $this->releaseSavepoint($this->_getNestedTransactionSavePointName());
        }

        --$this->transactionNestingLevel;

        $eventManager = $this->getEventManager();

        if ($eventManager->hasListeners(Events::onTransactionCommit)) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Subscribing to %s events is deprecated.',
                Events::onTransactionCommit,
            );

            $eventManager->dispatchEvent(Events::onTransactionCommit, new TransactionCommitEventArgs($this));
        }

        if ($this->autoCommit !== false || $this->transactionNestingLevel !== 0) {
            return $result;
        }

        $this->beginTransaction();

        return $result;
    }

    
    private function doCommit(DriverConnection $connection)
    {
        $logger = $this->_config->getSQLLogger();

        if ($logger !== null) {
            $logger->startQuery('"COMMIT"');
        }

        $result = $connection->commit();

        if ($logger !== null) {
            $logger->stopQuery();
        }

        return $result;
    }

    
    private function commitAll(): void
    {
        while ($this->transactionNestingLevel !== 0) {
            if ($this->autoCommit === false && $this->transactionNestingLevel === 1) {
                
                
                $this->commit();

                return;
            }

            $this->commit();
        }
    }

    
    public function rollBack()
    {
        if ($this->transactionNestingLevel === 0) {
            throw ConnectionException::noActiveTransaction();
        }

        $connection = $this->getWrappedConnection();

        $logger = $this->_config->getSQLLogger();

        if ($this->transactionNestingLevel === 1) {
            if ($logger !== null) {
                $logger->startQuery('"ROLLBACK"');
            }

            $this->transactionNestingLevel = 0;
            $connection->rollBack();
            $this->isRollbackOnly = false;
            if ($logger !== null) {
                $logger->stopQuery();
            }

            if ($this->autoCommit === false) {
                $this->beginTransaction();
            }
        } elseif ($this->nestTransactionsWithSavepoints) {
            if ($logger !== null) {
                $logger->startQuery('"ROLLBACK TO SAVEPOINT"');
            }

            $this->rollbackSavepoint($this->_getNestedTransactionSavePointName());
            --$this->transactionNestingLevel;
            if ($logger !== null) {
                $logger->stopQuery();
            }
        } else {
            $this->isRollbackOnly = true;
            --$this->transactionNestingLevel;
        }

        $eventManager = $this->getEventManager();

        if ($eventManager->hasListeners(Events::onTransactionRollBack)) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Subscribing to %s events is deprecated.',
                Events::onTransactionRollBack,
            );

            $eventManager->dispatchEvent(Events::onTransactionRollBack, new TransactionRollBackEventArgs($this));
        }

        return true;
    }

    
    public function createSavepoint($savepoint)
    {
        $platform = $this->getDatabasePlatform();

        if (! $platform->supportsSavepoints()) {
            throw ConnectionException::savepointsNotSupported();
        }

        $this->executeStatement($platform->createSavePoint($savepoint));
    }

    
    public function releaseSavepoint($savepoint)
    {
        $logger = $this->_config->getSQLLogger();

        $platform = $this->getDatabasePlatform();

        if (! $platform->supportsSavepoints()) {
            throw ConnectionException::savepointsNotSupported();
        }

        if (! $platform->supportsReleaseSavepoints()) {
            if ($logger !== null) {
                $logger->stopQuery();
            }

            return;
        }

        if ($logger !== null) {
            $logger->startQuery('"RELEASE SAVEPOINT"');
        }

        $this->executeStatement($platform->releaseSavePoint($savepoint));

        if ($logger === null) {
            return;
        }

        $logger->stopQuery();
    }

    
    public function rollbackSavepoint($savepoint)
    {
        $platform = $this->getDatabasePlatform();

        if (! $platform->supportsSavepoints()) {
            throw ConnectionException::savepointsNotSupported();
        }

        $this->executeStatement($platform->rollbackSavePoint($savepoint));
    }

    
    public function getWrappedConnection()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'Connection::getWrappedConnection() is deprecated.'
                . ' Use Connection::getNativeConnection() to access the native connection.',
        );

        $this->connect();

        assert($this->_conn !== null);

        return $this->_conn;
    }

    
    public function getNativeConnection()
    {
        $this->connect();

        assert($this->_conn !== null);
        if (! method_exists($this->_conn, 'getNativeConnection')) {
            throw new LogicException(sprintf(
                'The driver connection %s does not support accessing the native connection.',
                get_class($this->_conn),
            ));
        }

        return $this->_conn->getNativeConnection();
    }

    
    public function createSchemaManager(): AbstractSchemaManager
    {
        return $this->_driver->getSchemaManager(
            $this,
            $this->getDatabasePlatform(),
        );
    }

    
    public function getSchemaManager()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'Connection::getSchemaManager() is deprecated, use Connection::createSchemaManager() instead.',
        );

        return $this->_schemaManager ??= $this->createSchemaManager();
    }

    
    public function setRollbackOnly()
    {
        if ($this->transactionNestingLevel === 0) {
            throw ConnectionException::noActiveTransaction();
        }

        $this->isRollbackOnly = true;
    }

    
    public function isRollbackOnly()
    {
        if ($this->transactionNestingLevel === 0) {
            throw ConnectionException::noActiveTransaction();
        }

        return $this->isRollbackOnly;
    }

    
    public function convertToDatabaseValue($value, $type)
    {
        return Type::getType($type)->convertToDatabaseValue($value, $this->getDatabasePlatform());
    }

    
    public function convertToPHPValue($value, $type)
    {
        return Type::getType($type)->convertToPHPValue($value, $this->getDatabasePlatform());
    }

    
    private function bindParameters(DriverStatement $stmt, array $params, array $types): void
    {
        
        if (is_int(key($params))) {
            $bindIndex = 1;

            foreach ($params as $key => $value) {
                if (isset($types[$key])) {
                    $type                  = $types[$key];
                    [$value, $bindingType] = $this->getBindingInfo($value, $type);
                } else {
                    if (array_key_exists($key, $types)) {
                        Deprecation::trigger(
                            'doctrine/dbal',
                            'https:
                            'Using NULL as prepared statement parameter type is deprecated.'
                                . 'Omit or use Parameter::STRING instead',
                        );
                    }

                    $bindingType = ParameterType::STRING;
                }

                $stmt->bindValue($bindIndex, $value, $bindingType);

                ++$bindIndex;
            }
        } else {
            
            foreach ($params as $name => $value) {
                if (isset($types[$name])) {
                    $type                  = $types[$name];
                    [$value, $bindingType] = $this->getBindingInfo($value, $type);
                } else {
                    if (array_key_exists($name, $types)) {
                        Deprecation::trigger(
                            'doctrine/dbal',
                            'https:
                            'Using NULL as prepared statement parameter type is deprecated.'
                                . 'Omit or use Parameter::STRING instead',
                        );
                    }

                    $bindingType = ParameterType::STRING;
                }

                $stmt->bindValue($name, $value, $bindingType);
            }
        }
    }

    
    private function getBindingInfo($value, $type): array
    {
        if (is_string($type)) {
            $type = Type::getType($type);
        }

        if ($type instanceof Type) {
            $value       = $type->convertToDatabaseValue($value, $this->getDatabasePlatform());
            $bindingType = $type->getBindingType();
        } else {
            $bindingType = $type ?? ParameterType::STRING;
        }

        return [$value, $bindingType];
    }

    
    public function createQueryBuilder()
    {
        return new Query\QueryBuilder($this);
    }

    
    final public function convertExceptionDuringQuery(
        Driver\Exception $e,
        string $sql,
        array $params = [],
        array $types = []
    ): DriverException {
        return $this->handleDriverException($e, new Query($sql, $params, $types));
    }

    
    final public function convertException(Driver\Exception $e): DriverException
    {
        return $this->handleDriverException($e, null);
    }

    
    private function expandArrayParameters(string $sql, array $params, array $types): array
    {
        $this->parser ??= $this->getDatabasePlatform()->createSQLParser();
        $visitor        = new ExpandArrayParameters($params, $types);

        $this->parser->parse($sql, $visitor);

        return [
            $visitor->getSQL(),
            $visitor->getParameters(),
            $visitor->getTypes(),
        ];
    }

    
    private function needsArrayParameterConversion(array $params, array $types): bool
    {
        if (is_string(key($params))) {
            return true;
        }

        foreach ($types as $type) {
            if (
                $type === self::PARAM_INT_ARRAY
                || $type === self::PARAM_STR_ARRAY
                || $type === self::PARAM_ASCII_STR_ARRAY
            ) {
                return true;
            }
        }

        return false;
    }

    private function handleDriverException(
        Driver\Exception $driverException,
        ?Query $query
    ): DriverException {
        $this->exceptionConverter ??= $this->_driver->getExceptionConverter();
        $exception                  = $this->exceptionConverter->convert($driverException, $query);

        if ($exception instanceof ConnectionLost) {
            $this->close();
        }

        return $exception;
    }

    
    public function executeUpdate(string $sql, array $params = [], array $types = []): int
    {
        return $this->executeStatement($sql, $params, $types);
    }

    
    public function query(string $sql): Result
    {
        return $this->executeQuery($sql);
    }

    
    public function exec(string $sql): int
    {
        return $this->executeStatement($sql);
    }
}

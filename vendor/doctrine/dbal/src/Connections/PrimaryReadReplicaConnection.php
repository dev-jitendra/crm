<?php

namespace Doctrine\DBAL\Connections;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Statement;
use Doctrine\Deprecations\Deprecation;
use InvalidArgumentException;

use function array_rand;
use function count;


class PrimaryReadReplicaConnection extends Connection
{
    
    protected $connections = ['primary' => null, 'replica' => null];

    
    protected $keepReplica = false;

    
    public function __construct(
        array $params,
        Driver $driver,
        ?Configuration $config = null,
        ?EventManager $eventManager = null
    ) {
        if (! isset($params['replica'], $params['primary'])) {
            throw new InvalidArgumentException('primary or replica configuration missing');
        }

        if (count($params['replica']) === 0) {
            throw new InvalidArgumentException('You have to configure at least one replica.');
        }

        if (isset($params['driver'])) {
            $params['primary']['driver'] = $params['driver'];

            foreach ($params['replica'] as $replicaKey => $replica) {
                $params['replica'][$replicaKey]['driver'] = $params['driver'];
            }
        }

        $this->keepReplica = (bool) ($params['keepReplica'] ?? false);

        parent::__construct($params, $driver, $config, $eventManager);
    }

    
    public function isConnectedToPrimary(): bool
    {
        return $this->_conn !== null && $this->_conn === $this->connections['primary'];
    }

    
    public function connect($connectionName = null)
    {
        if ($connectionName !== null) {
            throw new InvalidArgumentException(
                'Passing a connection name as first argument is not supported anymore.'
                    . ' Use ensureConnectedToPrimary()/ensureConnectedToReplica() instead.',
            );
        }

        return $this->performConnect();
    }

    protected function performConnect(?string $connectionName = null): bool
    {
        $requestedConnectionChange = ($connectionName !== null);
        $connectionName            = $connectionName ?? 'replica';

        if ($connectionName !== 'replica' && $connectionName !== 'primary') {
            throw new InvalidArgumentException('Invalid option to connect(), only primary or replica allowed.');
        }

        
        
        
        if ($this->_conn !== null && ! $requestedConnectionChange) {
            return false;
        }

        $forcePrimaryAsReplica = false;

        if ($this->getTransactionNestingLevel() > 0) {
            $connectionName        = 'primary';
            $forcePrimaryAsReplica = true;
        }

        if (isset($this->connections[$connectionName])) {
            $this->_conn = $this->connections[$connectionName];

            if ($forcePrimaryAsReplica && ! $this->keepReplica) {
                $this->connections['replica'] = $this->_conn;
            }

            return false;
        }

        if ($connectionName === 'primary') {
            $this->connections['primary'] = $this->_conn = $this->connectTo($connectionName);

            
            if (! $this->keepReplica) {
                $this->connections['replica'] = $this->connections['primary'];
            }
        } else {
            $this->connections['replica'] = $this->_conn = $this->connectTo($connectionName);
        }

        if ($this->_eventManager->hasListeners(Events::postConnect)) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Subscribing to %s events is deprecated. Implement a middleware instead.',
                Events::postConnect,
            );

            $eventArgs = new ConnectionEventArgs($this);
            $this->_eventManager->dispatchEvent(Events::postConnect, $eventArgs);
        }

        return true;
    }

    
    public function ensureConnectedToPrimary(): bool
    {
        return $this->performConnect('primary');
    }

    
    public function ensureConnectedToReplica(): bool
    {
        return $this->performConnect('replica');
    }

    
    protected function connectTo($connectionName)
    {
        $params = $this->getParams();

        $connectionParams = $this->chooseConnectionConfiguration($connectionName, $params);

        try {
            return $this->_driver->connect($connectionParams);
        } catch (DriverException $e) {
            throw $this->convertException($e);
        }
    }

    
    protected function chooseConnectionConfiguration($connectionName, $params)
    {
        if ($connectionName === 'primary') {
            return $params['primary'];
        }

        $config = $params['replica'][array_rand($params['replica'])];

        if (! isset($config['charset']) && isset($params['primary']['charset'])) {
            $config['charset'] = $params['primary']['charset'];
        }

        return $config;
    }

    
    public function executeStatement($sql, array $params = [], array $types = [])
    {
        $this->ensureConnectedToPrimary();

        return parent::executeStatement($sql, $params, $types);
    }

    
    public function beginTransaction()
    {
        $this->ensureConnectedToPrimary();

        return parent::beginTransaction();
    }

    
    public function commit()
    {
        $this->ensureConnectedToPrimary();

        return parent::commit();
    }

    
    public function rollBack()
    {
        $this->ensureConnectedToPrimary();

        return parent::rollBack();
    }

    
    public function close()
    {
        unset($this->connections['primary'], $this->connections['replica']);

        parent::close();

        $this->_conn       = null;
        $this->connections = ['primary' => null, 'replica' => null];
    }

    
    public function createSavepoint($savepoint)
    {
        $this->ensureConnectedToPrimary();

        parent::createSavepoint($savepoint);
    }

    
    public function releaseSavepoint($savepoint)
    {
        $this->ensureConnectedToPrimary();

        parent::releaseSavepoint($savepoint);
    }

    
    public function rollbackSavepoint($savepoint)
    {
        $this->ensureConnectedToPrimary();

        parent::rollbackSavepoint($savepoint);
    }

    public function prepare(string $sql): Statement
    {
        $this->ensureConnectedToPrimary();

        return parent::prepare($sql);
    }
}

<?php

namespace Doctrine\DBAL\Driver\OCI8;

use Doctrine\DBAL\Driver\AbstractOracleDriver;
use Doctrine\DBAL\Driver\OCI8\Exception\ConnectionFailed;

use function oci_connect;
use function oci_pconnect;

use const OCI_NO_AUTO_COMMIT;


final class Driver extends AbstractOracleDriver
{
    
    public function connect(array $params)
    {
        $username    = $params['user'] ?? '';
        $password    = $params['password'] ?? '';
        $charset     = $params['charset'] ?? '';
        $sessionMode = $params['sessionMode'] ?? OCI_NO_AUTO_COMMIT;

        $connectionString = $this->getEasyConnectString($params);

        if (! empty($params['persistent'])) {
            $connection = @oci_pconnect($username, $password, $connectionString, $charset, $sessionMode);
        } else {
            $connection = @oci_connect($username, $password, $connectionString, $charset, $sessionMode);
        }

        if ($connection === false) {
            throw ConnectionFailed::new();
        }

        return new Connection($connection);
    }
}

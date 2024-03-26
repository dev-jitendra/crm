<?php


namespace Espo\ORM\PDO;

use Espo\ORM\DatabaseParams;

use PDO;

class Options
{
    
    public static function getOptionsFromDatabaseParams(DatabaseParams $databaseParams): array
    {
        $options = [];

        if ($databaseParams->getSslCa()) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $databaseParams->getSslCa();
        }

        if ($databaseParams->getSslCert()) {
            $options[PDO::MYSQL_ATTR_SSL_CERT] = $databaseParams->getSslCert();
        }

        if ($databaseParams->getSslKey()) {
            $options[PDO::MYSQL_ATTR_SSL_KEY] = $databaseParams->getSslKey();
        }

        if ($databaseParams->getSslCaPath()) {
            $options[PDO::MYSQL_ATTR_SSL_CAPATH] = $databaseParams->getSslCaPath();
        }

        if ($databaseParams->getSslCipher()) {
            $options[PDO::MYSQL_ATTR_SSL_CIPHER] = $databaseParams->getSslCipher();
        }

        if ($databaseParams->isSslVerifyDisabled()) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        return $options;
    }
}

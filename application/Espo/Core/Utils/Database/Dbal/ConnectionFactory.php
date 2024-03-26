<?php


namespace Espo\Core\Utils\Database\Dbal;

use Doctrine\DBAL\Connection;
use Espo\ORM\DatabaseParams;

interface ConnectionFactory
{
    public function create(DatabaseParams $databaseParams): Connection;
}

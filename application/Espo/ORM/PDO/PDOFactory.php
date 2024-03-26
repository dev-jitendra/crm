<?php


namespace Espo\ORM\PDO;

use Espo\ORM\DatabaseParams;
use PDO;

interface PDOFactory
{
    public function create(DatabaseParams $databaseParams): PDO;
}

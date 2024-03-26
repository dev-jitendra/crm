<?php


namespace Espo\ORM\PDO;

use PDO;

interface PDOProvider
{
    public function get(): PDO;
}

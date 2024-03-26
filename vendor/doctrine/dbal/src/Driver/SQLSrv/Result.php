<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Driver\SQLSrv;

use Doctrine\DBAL\Driver\FetchUtils;
use Doctrine\DBAL\Driver\Result as ResultInterface;

use function sqlsrv_fetch;
use function sqlsrv_fetch_array;
use function sqlsrv_num_fields;
use function sqlsrv_rows_affected;

use const SQLSRV_FETCH_ASSOC;
use const SQLSRV_FETCH_NUMERIC;

final class Result implements ResultInterface
{
    
    private $statement;

    
    public function __construct($stmt)
    {
        $this->statement = $stmt;
    }

    
    public function fetchNumeric()
    {
        return $this->fetch(SQLSRV_FETCH_NUMERIC);
    }

    
    public function fetchAssociative()
    {
        return $this->fetch(SQLSRV_FETCH_ASSOC);
    }

    
    public function fetchOne()
    {
        return FetchUtils::fetchOne($this);
    }

    
    public function fetchAllNumeric(): array
    {
        return FetchUtils::fetchAllNumeric($this);
    }

    
    public function fetchAllAssociative(): array
    {
        return FetchUtils::fetchAllAssociative($this);
    }

    
    public function fetchFirstColumn(): array
    {
        return FetchUtils::fetchFirstColumn($this);
    }

    public function rowCount(): int
    {
        $count = sqlsrv_rows_affected($this->statement);

        if ($count !== false) {
            return $count;
        }

        return 0;
    }

    public function columnCount(): int
    {
        $count = sqlsrv_num_fields($this->statement);

        if ($count !== false) {
            return $count;
        }

        return 0;
    }

    public function free(): void
    {
        
        
        
        
        while (sqlsrv_fetch($this->statement)) {
        }
    }

    
    private function fetch(int $fetchType)
    {
        return sqlsrv_fetch_array($this->statement, $fetchType) ?? false;
    }
}

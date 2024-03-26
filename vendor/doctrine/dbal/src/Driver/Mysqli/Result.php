<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Driver\Mysqli;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\FetchUtils;
use Doctrine\DBAL\Driver\Mysqli\Exception\StatementError;
use Doctrine\DBAL\Driver\Result as ResultInterface;
use mysqli_sql_exception;
use mysqli_stmt;

use function array_column;
use function array_combine;
use function array_fill;
use function count;

final class Result implements ResultInterface
{
    private mysqli_stmt $statement;

    
    private bool $hasColumns = false;

    
    private array $columnNames = [];

    
    private array $boundValues = [];

    
    public function __construct(mysqli_stmt $statement)
    {
        $this->statement = $statement;

        $meta = $statement->result_metadata();

        if ($meta === false) {
            return;
        }

        $this->hasColumns = true;

        $this->columnNames = array_column($meta->fetch_fields(), 'name');

        $meta->free();

        
        
        
        $this->statement->store_result();

        
        
        
        
        
        
        
        
        
        
        
        $this->boundValues = array_fill(0, count($this->columnNames), null);

        
        $refs = &$this->boundValues;

        if (! $this->statement->bind_result(...$refs)) {
            throw StatementError::new($this->statement);
        }
    }

    
    public function fetchNumeric()
    {
        try {
            $ret = $this->statement->fetch();
        } catch (mysqli_sql_exception $e) {
            throw StatementError::upcast($e);
        }

        if ($ret === false) {
            throw StatementError::new($this->statement);
        }

        if ($ret === null) {
            return false;
        }

        $values = [];

        foreach ($this->boundValues as $v) {
            $values[] = $v;
        }

        return $values;
    }

    
    public function fetchAssociative()
    {
        $values = $this->fetchNumeric();

        if ($values === false) {
            return false;
        }

        return array_combine($this->columnNames, $values);
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
        if ($this->hasColumns) {
            return $this->statement->num_rows;
        }

        return $this->statement->affected_rows;
    }

    public function columnCount(): int
    {
        return $this->statement->field_count;
    }

    public function free(): void
    {
        $this->statement->free_result();
    }
}

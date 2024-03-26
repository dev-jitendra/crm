<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Driver;


interface Result
{
    
    public function fetchNumeric();

    
    public function fetchAssociative();

    
    public function fetchOne();

    
    public function fetchAllNumeric(): array;

    
    public function fetchAllAssociative(): array;

    
    public function fetchFirstColumn(): array;

    
    public function rowCount(): int;

    
    public function columnCount(): int;

    
    public function free(): void;
}

<?php

declare(strict_types=1);

namespace Doctrine\DBAL;

final class TransactionIsolationLevel
{
    
    public const READ_UNCOMMITTED = 1;

    
    public const READ_COMMITTED = 2;

    
    public const REPEATABLE_READ = 3;

    
    public const SERIALIZABLE = 4;

    
    private function __construct()
    {
    }
}

<?php

namespace Doctrine\DBAL;


class LockMode
{
    public const NONE              = 0;
    public const OPTIMISTIC        = 1;
    public const PESSIMISTIC_READ  = 2;
    public const PESSIMISTIC_WRITE = 4;

    
    final private function __construct()
    {
    }
}

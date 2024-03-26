<?php


namespace Espo\ORM\Query;

use RuntimeException;


class LockTable implements Query
{
    use BaseTrait;

    public const MODE_SHARE = 'SHARE';
    public const MODE_EXCLUSIVE = 'EXCLUSIVE';

    
    protected function validateRawParams(array $params): void
    {
        if (empty($params['table'])) {
            throw new RuntimeException("LockTable params: No table specified.");
        }

        if (empty($params['mode'])) {
            throw new RuntimeException("LockTable params: No mode specified.");
        }
    }
}

<?php


namespace Espo\ORM\Query;

use RuntimeException;


class Union implements SelectingQuery
{
    use BaseTrait;

    
    private function validateRawParams(array $params): void
    {
        if (empty($params['queries'])) {
            throw new RuntimeException("Union params: No query were added.");
        }
    }
}

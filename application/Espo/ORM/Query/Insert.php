<?php


namespace Espo\ORM\Query;

use RuntimeException;


class Insert implements Query
{
    use BaseTrait;

    
    private function validateRawParams(array $params): void
    {
        $into = $params['into'] ?? null;

        if (!$into || !is_string($into)) {
            throw new RuntimeException("Bad or missing 'into' parameter.");
        }

        $columns = $params['columns'] ?? [];

        if (!is_array($columns)) {
            throw new RuntimeException("Bad 'columns' parameter.");
        }

        $values = $params['values'] ?? [];

        if (!is_array($values)) {
            throw new RuntimeException("Bad 'values' parameter.");
        }

        $updateSet = $params['updateSet'] ?? null;

        if ($updateSet && !is_array($updateSet)) {
            throw new RuntimeException("Bad 'updateSet' parameter.");
        }
    }
}

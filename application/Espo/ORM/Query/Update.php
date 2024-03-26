<?php


namespace Espo\ORM\Query;

use Espo\ORM\Query\Part\Expression;
use RuntimeException;


class Update implements Query
{
    use SelectingTrait;
    use BaseTrait;

    
    public function getIn(): string
    {
        $in = $this->params['from'];

        if ($in === null) {
            throw new RuntimeException("Missing 'in'.");
        }

        return $in;
    }

    
    public function getLimit(): ?int
    {
        return $this->params['limit'] ?? null;
    }

    
    public function getSet(): array
    {
        $set = [];
        
        $raw = $this->params['set'];

        foreach ($raw as $key => $value) {
            if (str_ends_with($key, ':')) {
                $key = substr($key, 0, -1);
                $value = Expression::create((string) $value);
            }

            $set[$key] = $value;
        }

        return $set;
    }

    
    private function validateRawParams(array $params): void
    {
        $this->validateRawParamsSelecting($params);

        $from = $params['from'] ?? null;

        if (!$from || !is_string($from)) {
            throw new RuntimeException("Update params: Missing 'in'.");
        }

        $set = $params['set'] ?? null;

        if (!$set || !is_array($set)) {
            throw new RuntimeException("Update params: Bad or missing 'set' parameter.");
        }
    }
}

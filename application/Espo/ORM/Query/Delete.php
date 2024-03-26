<?php


namespace Espo\ORM\Query;

use RuntimeException;


class Delete implements Query
{
    use SelectingTrait;
    use BaseTrait;

    
    public function getFrom(): string
    {
        return $this->params['from'];
    }

    
    public function getFromAlias(): ?string
    {
        return $this->params['fromAlias'] ?? null;
    }

    
    public function getLimit(): ?int
    {
        return $this->params['limit'] ?? null;
    }

    
    private function validateRawParams(array $params): void
    {
        $this->validateRawParamsSelecting($params);

        $from = $params['from'] ?? null;

        if (!$from || !is_string($from)) {
            throw new RuntimeException("Select params: Missing 'from'.");
        }
    }
}

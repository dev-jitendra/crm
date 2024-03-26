<?php


namespace Espo\ORM\Query;

use RuntimeException;

trait BaseBuilderTrait
{
    
    protected $params = [];

    public function __construct()
    {
    }

    private function isEmpty(): bool
    {
        return empty($this->params);
    }

    private function cloneInternal(Query $query): void
    {
        if (!$this->isEmpty()) {
            throw new RuntimeException("Clone can be called only on a new empty builder instance.");
        }

        $this->params = $query->getRaw();
    }
}

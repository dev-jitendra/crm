<?php


namespace Espo\ORM\Query;

trait BaseTrait
{
    
    private $params = [];

    
    public function getRaw(): array
    {
        return $this->params;
    }

    
    public static function fromRaw(array $params): self
    {
        $obj = new self();

        $obj->validateRawParams($params);

        $obj->params = $params;

        return $obj;
    }

    
    private function validateRawParams(array $params): void
    {}
}

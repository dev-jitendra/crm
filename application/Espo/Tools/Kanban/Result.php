<?php


namespace Espo\Tools\Kanban;

use Espo\ORM\Entity;
use Espo\ORM\EntityCollection;
use stdClass;

class Result
{
    
    private EntityCollection $collection;
    private int $total;
    private stdClass $data;

    
    public function __construct(EntityCollection $collection, int $total, stdClass $data)
    {
        $this->collection = $collection;
        $this->total = $total;
        $this->data = $data;
    }

    
    public function getCollection(): EntityCollection
    {
        return $this->collection;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getData(): stdClass
    {
        return $this->data;
    }
}

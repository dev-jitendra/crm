<?php


namespace Espo\Core\Action;

use RuntimeException;


class Params
{
    public function __construct(private string $entityType, private string $id)
    {

        if (!$entityType || !$id) {
            throw new RuntimeException();
        }
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getId(): string
    {
        return $this->id;
    }
}

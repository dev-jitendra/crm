<?php


namespace Espo\Tools\LinkManager;

use Espo\Tools\LinkManager\ParamsBuilder;


class Params
{
    private string $type;
    private string $entityType;
    private string $link;
    private string $foreignLink;
    private ?string $foreignEntityType;
    private ?string $name;

    public function __construct(
        string $type,
        string $entityType,
        string $link,
        ?string $foreignEntityType,
        string $foreignLink,
        ?string $name
    ) {
        $this->type = $type;
        $this->entityType = $entityType;
        $this->link = $link;
        $this->foreignEntityType = $foreignEntityType;
        $this->foreignLink = $foreignLink;
        $this->name = $name;
    }

    public static function createBuilder(): ParamsBuilder
    {
        return new ParamsBuilder();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getForeignLink(): string
    {
        return $this->foreignLink;
    }

    public function getForeignEntityType(): ?string
    {
        return $this->foreignEntityType;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}

<?php


namespace Espo\Tools\ExportCustom;

class Params
{
    public function __construct(
        private string $name,
        private string $module,
        private string $version,
        private string $author,
        private ?string $description
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}

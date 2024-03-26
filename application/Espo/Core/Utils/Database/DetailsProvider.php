<?php


namespace Espo\Core\Utils\Database;

interface DetailsProvider
{
    public function getType(): string;

    public function getVersion(): string;

    public function getServerVersion(): string;

    public function getParam(string $name): ?string;
}

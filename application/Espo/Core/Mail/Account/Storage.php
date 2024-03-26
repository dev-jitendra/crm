<?php


namespace Espo\Core\Mail\Account;

use Espo\Core\Field\DateTime;

interface Storage
{
    
    public function setFlags(int $id, array $flags): void;

    
    public function getSize(int $id): int;

    
    public function getRawContent(int $id): string;

    
    public function getUniqueId(int $id): string;

    
    public function getIdsFromUniqueId(string $uniqueId): array;

    
    public function getIdsSinceDate(DateTime $since): array;

    
    public function getHeaderAndFlags(int $id): array;

    
    public function close(): void;

    
    public function getFolderNames(): array;

    
    public function selectFolder(string $name): void;

    
    public function appendMessage(string $content, ?string $folder = null): void;
}

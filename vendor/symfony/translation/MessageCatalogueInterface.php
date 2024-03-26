<?php



namespace Symfony\Component\Translation;

use Symfony\Component\Config\Resource\ResourceInterface;


interface MessageCatalogueInterface
{
    public const INTL_DOMAIN_SUFFIX = '+intl-icu';

    
    public function getLocale(): string;

    
    public function getDomains(): array;

    
    public function all(string $domain = null): array;

    
    public function set(string $id, string $translation, string $domain = 'messages');

    
    public function has(string $id, string $domain = 'messages'): bool;

    
    public function defines(string $id, string $domain = 'messages'): bool;

    
    public function get(string $id, string $domain = 'messages'): string;

    
    public function replace(array $messages, string $domain = 'messages');

    
    public function add(array $messages, string $domain = 'messages');

    
    public function addCatalogue(self $catalogue);

    
    public function addFallbackCatalogue(self $catalogue);

    
    public function getFallbackCatalogue(): ?self;

    
    public function getResources(): array;

    
    public function addResource(ResourceInterface $resource);
}

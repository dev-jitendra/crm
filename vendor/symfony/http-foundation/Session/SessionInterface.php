<?php



namespace Symfony\Component\HttpFoundation\Session;

use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;


interface SessionInterface
{
    
    public function start(): bool;

    
    public function getId(): string;

    
    public function setId(string $id);

    
    public function getName(): string;

    
    public function setName(string $name);

    
    public function invalidate(int $lifetime = null): bool;

    
    public function migrate(bool $destroy = false, int $lifetime = null): bool;

    
    public function save();

    
    public function has(string $name): bool;

    
    public function get(string $name, mixed $default = null): mixed;

    
    public function set(string $name, mixed $value);

    
    public function all(): array;

    
    public function replace(array $attributes);

    
    public function remove(string $name): mixed;

    
    public function clear();

    
    public function isStarted(): bool;

    
    public function registerBag(SessionBagInterface $bag);

    
    public function getBag(string $name): SessionBagInterface;

    
    public function getMetadataBag(): MetadataBag;
}

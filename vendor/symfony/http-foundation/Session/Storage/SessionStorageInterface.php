<?php



namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;


interface SessionStorageInterface
{
    
    public function start(): bool;

    
    public function isStarted(): bool;

    
    public function getId(): string;

    
    public function setId(string $id);

    
    public function getName(): string;

    
    public function setName(string $name);

    
    public function regenerate(bool $destroy = false, int $lifetime = null): bool;

    
    public function save();

    
    public function clear();

    
    public function getBag(string $name): SessionBagInterface;

    
    public function registerBag(SessionBagInterface $bag);

    public function getMetadataBag(): MetadataBag;
}

<?php



namespace Symfony\Component\HttpFoundation\Session;


interface SessionBagInterface
{
    
    public function getName(): string;

    
    public function initialize(array &$array);

    
    public function getStorageKey(): string;

    
    public function clear(): mixed;
}

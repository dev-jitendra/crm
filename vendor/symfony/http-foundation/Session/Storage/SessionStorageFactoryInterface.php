<?php



namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Request;


interface SessionStorageFactoryInterface
{
    
    public function createStorage(?Request $request): SessionStorageInterface;
}

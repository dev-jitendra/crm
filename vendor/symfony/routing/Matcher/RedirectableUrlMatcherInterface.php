<?php



namespace Symfony\Component\Routing\Matcher;


interface RedirectableUrlMatcherInterface
{
    
    public function redirect(string $path, string $route, string $scheme = null): array;
}

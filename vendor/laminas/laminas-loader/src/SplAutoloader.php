<?php 

namespace Laminas\Loader;

use Traversable;

use function interface_exists;

if (interface_exists(SplAutoloader::class)) {
    return;
}


interface SplAutoloader
{
    
    public function __construct($options = null);

    
    public function setOptions($options);

    
    public function autoload($class);

    
    public function register();
}

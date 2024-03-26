<?php 

namespace Laminas\Loader;

use IteratorAggregate;
use Traversable;


interface PluginClassLocator extends ShortNameLocator, IteratorAggregate
{
    
    public function registerPlugin($shortName, $className);

    
    public function unregisterPlugin($shortName);

    
    public function getRegisteredPlugins();
}

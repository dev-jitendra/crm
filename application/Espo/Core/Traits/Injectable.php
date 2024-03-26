<?php


namespace Espo\Core\Traits;


trait Injectable
{
    protected $injections = []; 

    
    public function inject($name, $object)
    {
        $this->injections[$name] = $object;
    }

    
    public function getDependencyList(): array
    {
        return $this->dependencyList;
    }

    
    protected function getInjection(string $name)
    {
        return $this->injections[$name] ?? $this->$name ?? null;
    }

    
    protected function addDependency(string $name)
    {
        $this->dependencyList[] = $name;
    }

    
    protected function addDependencyList(array $list)
    {
        foreach ($list as $item) {
            $this->addDependency($item);
        }
    }
}

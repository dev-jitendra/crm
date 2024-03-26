<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Exceptions\Error;

use Espo\Core\Interfaces\Injectable;

use Espo\ORM\Entity;

use Espo\Core\Formula\Processor;
use Espo\Core\Formula\Argument;

use stdClass;


abstract class Base implements Injectable
{
    
    protected $name;

    
    protected $processor;

    
    private $entity;

    
    private $variables;

    protected $dependencyList = []; 

    protected $injections = []; 

    public function inject($name, $object) 
    {
        $this->injections[$name] = $object;
    }

    protected function getInjection($name) 
    {
        return $this->injections[$name] ?? $this->$name ?? null;
    }

    protected function addDependency($name) 
    {
        $this->dependencyList[] = $name;
    }

    protected function addDependencyList(array $list) 
    {
        foreach ($list as $item) {
            $this->addDependency($item);
        }
    }

    public function getDependencyList() 
    {
        return $this->dependencyList;
    }

    public function __construct(string $name, Processor $processor, ?Entity $entity = null, ?stdClass $variables = null)
    {
        $this->name = $name;
        $this->processor = $processor;
        $this->entity = $entity;
        $this->variables = $variables;

        $this->init();
    }

    protected function init() 
    {
    }

    protected function getVariables(): stdClass
    {
        return $this->variables ?? (object) [];
    }

    protected function getEntity() 
    {
        if (!$this->entity) {
            throw new Error('Formula: Entity required but not passed.');
        }

        return $this->entity;
    }

    
    public abstract function process(stdClass $item);

    
    protected function evaluate($item)
    {
        $item = new Argument($item);

        return $this->processor->process($item);
    }

    
    protected function fetchArguments(stdClass $item): array
    {
        $args = $item->value ?? [];

        $eArgs = [];

        foreach ($args as $item) {
            $eArgs[] = $this->evaluate($item);
        }

        return $eArgs;
    }

    
    protected function fetchRawArguments(stdClass $item): array
    {
        return $item->value ?? [];
    }
}

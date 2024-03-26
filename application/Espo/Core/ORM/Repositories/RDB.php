<?php


namespace Espo\Core\ORM\Repositories;

use Espo\Core\ORM\EntityManager;
use Espo\Core\ORM\EntityFactory;
use Espo\Core\Interfaces\Injectable;
use Espo\Core\ApplicationState;
use Espo\Core\HookManager;
use Espo\Core\Utils\Id\RecordIdGenerator;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\SystemUser;


class RDB extends \Espo\Core\Repositories\Database implements Injectable 
{
    protected $dependencyList = [ 
        'config',
    ];

    protected $dependencies = []; 

    protected $injections = []; 

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

    public function inject($name, $object) 
    {
        $this->injections[$name] = $object;
    }

    protected function getInjection($name) 
    {
        return $this->injections[$name] ?? $this->$name ?? null;
    }

    public function getDependencyList() 
    {
        return array_merge($this->dependencyList, $this->dependencies);
    }

    protected function getMetadata() 
    {
        return $this->getInjection('metadata');
    }

    protected function getConfig() 
    {
        return $this->getInjection('config');
    }

    public function __construct(
        string $entityType,
        EntityManager $entityManager,
        EntityFactory $entityFactory,
        Metadata $metadata,
        HookManager $hookManager,
        ApplicationState $applicationState,
        RecordIdGenerator $recordIdGenerator,
        SystemUser $systemUser
    ) {
        parent::__construct(
            $entityType,
            $entityManager,
            $entityFactory,
            $metadata,
            $hookManager,
            $applicationState,
            $recordIdGenerator,
            $systemUser
        );

        $this->init();
    }

    protected function init() 
    {
    }
}

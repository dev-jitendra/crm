<?php


namespace Espo\Core\Record;

use Espo\ORM\Entity;
use Espo\ORM\Repository\Util as RepositoryUtil;


class ServiceContainer
{
    
    private $data = [];

    public function __construct(private ServiceFactory $serviceFactory)
    {}

    
    public function getByClass(string $className): Service
    {
        $entityType = RepositoryUtil::getEntityTypeByClass($className);

        
        return $this->get($entityType);
    }

    
    public function get(string $entityType): Service
    {
        if (!array_key_exists($entityType, $this->data)) {
            $this->load($entityType);
        }

        return $this->data[$entityType];
    }

    private function load(string $entityType): void
    {
        $this->data[$entityType] = $this->serviceFactory->create($entityType);
    }
}

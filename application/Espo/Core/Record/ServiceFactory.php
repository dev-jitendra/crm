<?php


namespace Espo\Core\Record;

use Espo\Core\ServiceFactory as Factory;
use Espo\Core\Utils\Metadata;
use Espo\Entities\User;
use Espo\Core\Acl;
use Espo\Core\AclManager;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Util as RepositoryUtil;

use RuntimeException;


class ServiceFactory
{
    private const RECORD_SERVICE_NAME = 'Record';
    private const RECORD_TREE_SERVICE_NAME = 'RecordTree';

    
    private $defaultTypeMap = [
        'CategoryTree' => self::RECORD_TREE_SERVICE_NAME,
    ];

    public function __construct(
        private Factory $serviceFactory,
        private Metadata $metadata,
        private User $user,
        private Acl $acl,
        private AclManager $aclManager
    ) {}

    
    public function createByClass(string $className): Service
    {
        $entityType = RepositoryUtil::getEntityTypeByClass($className);

        
        return $this->create($entityType);
    }

    
    public function createByClassForUser(string $className, User $user): Service
    {
        $entityType = RepositoryUtil::getEntityTypeByClass($className);

        
        return $this->createForUser($entityType, $user);
    }

    
    public function create(string $entityType): Service
    {
        $obj = $this->createInternal($entityType);

        $obj->setUser($this->user);
        $obj->setAcl($this->acl);

        return $obj;
    }

    
    public function createForUser(string $entityType, User $user): Service
    {
        $obj = $this->createInternal($entityType);

        $acl = $this->aclManager->createUserAcl($user);

        $obj->setUser($user);
        $obj->setAcl($acl);

        return $obj;
    }

    
    private function createInternal(string $entityType): Service
    {
        if (!$this->metadata->get(['scopes', $entityType, 'entity'])) {
            throw new RuntimeException("Can't create record service '{$entityType}', there's no such entity type.");
        }

        if (!$this->serviceFactory->checkExists($entityType)) {
            return $this->createDefault($entityType);
        }

        $service = $this->serviceFactory->createWith($entityType, ['entityType' => $entityType]);

        if (!$service instanceof Service) {
            return $this->createDefault($entityType);
        }

        return $service;
    }

    
    private function createDefault(string $entityType): Service
    {
        $default = self::RECORD_SERVICE_NAME;

        $type = $this->metadata->get(['scopes', $entityType, 'type']);

        if ($type) {
            $default = $this->defaultTypeMap[$type] ?? $default;
        }

        $obj = $this->serviceFactory->createWith($default, ['entityType' => $entityType]);

        if (!$obj instanceof Service) {
            throw new RuntimeException("Service class {$default} is not instance of Record.");
        }

        return $obj;
    }
}

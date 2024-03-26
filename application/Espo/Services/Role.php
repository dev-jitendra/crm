<?php


namespace Espo\Services;

use Espo\Core\Acl\Cache\Clearer as AclCacheClearer;
use Espo\ORM\Entity;

use Espo\Core\Di;


class Role extends Record implements

    Di\DataManagerAware
{
    use Di\DataManagerSetter;

    protected $forceSelectAllAttributes = true;

    public function afterCreateEntity(Entity $entity, $data)
    {
        parent::afterCreateEntity($entity, $data);

        $this->clearRolesCache();
    }

    public function afterUpdateEntity(Entity $entity, $data)
    {
        parent::afterUpdateEntity($entity, $data);

        $this->clearRolesCache();
    }

    protected function clearRolesCache(): void
    {
        $this->createAclCacheClearer()->clearForAllInternalUsers();

        $this->dataManager->updateCacheTimestamp();
    }

    private function createAclCacheClearer(): AclCacheClearer
    {
        return $this->injectableFactory->create(AclCacheClearer::class);
    }
}

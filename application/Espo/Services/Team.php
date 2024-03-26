<?php


namespace Espo\Services;

use Espo\Core\Acl\Cache\Clearer as AclCacheClearer;
use Espo\Entities\User as UserEntity;
use Espo\ORM\Entity;

use Espo\Core\Select\SearchParams;

use Espo\Core\Di;


class Team extends Record implements

    Di\DataManagerAware
{
    use Di\DataManagerSetter;

    public function afterUpdateEntity(Entity $entity, $data)
    {
        parent::afterUpdateEntity($entity, $data);

        if (property_exists($data, 'rolesIds')) {
            $this->clearRolesCache();
        }
    }

    protected function clearRolesCache(): void
    {
        $this->createAclCacheClearer()->clearForAllInternalUsers();

        $this->dataManager->updateCacheTimestamp();
    }

    public function link(string $id, string $link, string $foreignId): void
    {
        parent::link($id, $link, $foreignId);

        if ($link === 'users') {
            
            $user = $this->entityManager->getEntityById(UserEntity::ENTITY_TYPE, $foreignId);

            if ($user) {
                $this->createAclCacheClearer()->clearForUser($user);
            }

            $this->dataManager->updateCacheTimestamp();
        }
    }

    public function unlink(string $id, string $link, string $foreignId): void
    {
        parent::unlink($id, $link, $foreignId);

        if ($link === 'users') {
            
            $user = $this->entityManager->getEntityById(UserEntity::ENTITY_TYPE, $foreignId);

            if ($user) {
                $this->createAclCacheClearer()->clearForUser($user);
            }

            $this->dataManager->updateCacheTimestamp();
        }
    }

    public function massLink(string $id, string $link, SearchParams $searchParams): bool
    {
        $result = parent::massLink($id, $link, $searchParams);

        if ($link === 'users') {
            $this->clearRolesCache();
        }

        return $result;
    }

    private function createAclCacheClearer(): AclCacheClearer
    {
        return $this->injectableFactory->create(AclCacheClearer::class);
    }
}

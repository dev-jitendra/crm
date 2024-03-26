<?php


namespace Espo\Services;

use Espo\Core\Acl\Cache\Clearer as AclCacheClearer;
use Espo\ORM\Entity;
use Espo\Repositories\Portal as Repository;
use Espo\Entities\Portal as PortalEntity;
use Espo\Core\Di;
use stdClass;


class Portal extends Record implements

    Di\DataManagerAware
{
    use Di\DataManagerSetter;

    protected $getEntityBeforeUpdate = true;

    protected $mandatorySelectAttributeList = [
        'customUrl',
        'customId',
    ];

    public function filterCreateInput(stdClass $data): void
    {
        parent::filterCreateInput($data);

        $this->filterRestrictedFields($data);
    }

    public function filterUpdateInput(stdClass $data): void
    {
        parent::filterUpdateInput($data);

        $this->filterRestrictedFields($data);
    }

    private function filterRestrictedFields(stdClass $data): void
    {
        if (!$this->config->get('restrictedMode')) {
            return;
        }

        if ($this->user->isSuperAdmin()) {
            return;
        }

        unset($data->customUrl);
    }

    protected function afterUpdateEntity(Entity $entity, $data)
    {
        

        $this->loadUrlField($entity);

        if (property_exists($data, 'portalRolesIds')) {
            $this->clearRolesCache();
        }
    }

    protected function loadUrlField(PortalEntity $entity): void
    {
        $this->getPortalRepository()->loadUrlField($entity);
    }

    protected function clearRolesCache(): void
    {
        $this->createAclCacheClearer()->clearForAllPortalUsers();

        $this->dataManager->updateCacheTimestamp();
    }

    private function getPortalRepository(): Repository
    {
        
        return $this->getRepository();
    }

    private function createAclCacheClearer(): AclCacheClearer
    {
        return $this->injectableFactory->create(AclCacheClearer::class);
    }
}

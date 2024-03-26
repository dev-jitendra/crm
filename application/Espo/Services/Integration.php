<?php


namespace Espo\Services;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Config\ConfigWriter;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

use stdClass;

class Integration
{
    
    protected $entityManager;

    
    protected $user;

    
    protected $config;

    
    protected $configWriter;

    public function __construct(
        EntityManager $entityManager,
        User $user,
        Config $config,
        ConfigWriter $configWriter
    ) {
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->config = $config;
        $this->configWriter = $configWriter;
    }

    
    protected function processAccessCheck()
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }
    }

    
    public function read(string $id): Entity
    {
        $this->processAccessCheck();

        $entity = $this->entityManager->getEntity('Integration', $id);

        if (!$entity) {
            throw new NotFound();
        }

        return $entity;
    }

    
    public function update(string $id, stdClass $data): Entity
    {
        $this->processAccessCheck();

        $entity = $this->entityManager->getEntity('Integration', $id);

        if (!$entity) {
            throw new NotFound();
        }

        $entity->set($data);

        $this->entityManager->saveEntity($entity);

        $integrationsConfigData = $this->config->get('integrations') ?? (object) [];

        if (!($integrationsConfigData instanceof stdClass)) {
            $integrationsConfigData = (object) [];
        }

        $integrationName = $id;

        $integrationsConfigData->$integrationName = $entity->get('enabled');

        $this->configWriter->set('integrations', $integrationsConfigData);

        $this->configWriter->save();

        return $entity;
    }
}

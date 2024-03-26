<?php


namespace Espo\Core\Loaders;

use Espo\Core\ApplicationState;
use Espo\Core\Container\Loader;
use Espo\Core\ORM\EntityManager;

use Espo\Core\Utils\SystemUser;
use Espo\Entities\Preferences as PreferencesEntity;

class Preferences implements Loader
{
    public function __construct(
        private EntityManager $entityManager,
        private ApplicationState $applicationState,
        private SystemUser $systemUser
    ) {}

    public function load(): PreferencesEntity
    {
        $id = $this->applicationState->hasUser() ?
            $this->applicationState->getUser()->getId() :
            $this->systemUser->getId();

        
        return $this->entityManager->getEntity(PreferencesEntity::ENTITY_TYPE, $id);
    }
}

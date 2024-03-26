<?php


namespace Espo\Tools\App;

use Espo\ORM\EntityManager;

use Espo\Repositories\Preferences as Repository;
use Espo\Entities\Preferences;
use Espo\Entities\User;

use Espo\Core\Acl;
use Espo\Core\Acl\Table;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\FieldValidation\FieldValidationManager;
use Espo\Core\Utils\Config;

use stdClass;

class PreferencesService
{
    private EntityManager $entityManager;
    private User $user;
    private Acl $acl;
    private Config $config;
    private FieldValidationManager $fieldValidationManager;

    public function __construct(
        EntityManager $entityManager,
        User $user,
        Acl $acl,
        Config $config,
        FieldValidationManager $fieldValidationManager
    ) {
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->acl = $acl;
        $this->config = $config;
        $this->fieldValidationManager = $fieldValidationManager;
    }

    
    protected function processAccessCheck(string $userId): void
    {
        if (!$this->user->isAdmin()) {
            if ($this->user->getId() !== $userId) {
                throw new Forbidden();
            }
        }
    }

    
    public function read(string $userId): Preferences
    {
        $this->processAccessCheck($userId);

        
        $entity = $this->entityManager->getEntityById(Preferences::ENTITY_TYPE, $userId);
        
        $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $userId);

        if (!$entity || !$user) {
            throw new NotFound();
        }

        $entity->set('name', $user->getName());
        $entity->set('isPortalUser', $user->isPortal());

        
        $entity->clear('smtpPassword');

        $forbiddenAttributeList = $this->acl
            ->getScopeForbiddenAttributeList(Preferences::ENTITY_TYPE, Table::ACTION_READ);

        foreach ($forbiddenAttributeList as $attribute) {
            $entity->clear($attribute);
        }

        return $entity;
    }

    
    public function update(string $userId, stdClass $data): Preferences
    {
        $this->processAccessCheck($userId);

        if ($this->acl->getLevel(Preferences::ENTITY_TYPE, Table::ACTION_EDIT) === Table::LEVEL_NO) {
            throw new Forbidden();
        }

        $forbiddenAttributeList = $this->acl
            ->getScopeForbiddenAttributeList(Preferences::ENTITY_TYPE, Table::ACTION_EDIT);

        foreach ($forbiddenAttributeList as $attribute) {
            unset($data->$attribute);
        }

        
        $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $userId);

        
        $entity = $this->entityManager->getEntityById(Preferences::ENTITY_TYPE, $userId);

        if (!$entity || !$user) {
            throw new NotFound();
        }

        $entity->set($data);

        $this->fieldValidationManager->process($entity, $data);

        $this->entityManager->saveEntity($entity);

        $entity->set('name', $user->getName());

        
        $entity->clear('smtpPassword');

        return $entity;
    }

    
    public function resetToDefaults(string $userId): void
    {
        $this->processAccessCheck($userId);

        $result = $this->getRepository()->resetToDefaults($userId);

        if (!$result) {
            throw new NotFound();
        }
    }

    
    public function resetDashboard(string $userId): stdClass
    {
        $this->processAccessCheck($userId);

        if ($this->acl->getLevel(Preferences::ENTITY_TYPE, Table::ACTION_EDIT) === Table::LEVEL_NO) {
            throw new Forbidden();
        }

        
        $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $userId);

        $preferences = $this->entityManager->getEntityById(Preferences::ENTITY_TYPE, $userId);

        if (!$user) {
            throw new NotFound();
        }

        if (!$preferences) {
            throw new NotFound();
        }

        if ($user->isPortal()) {
            throw new Forbidden();
        }

        $forbiddenAttributeList = $this->acl
            ->getScopeForbiddenAttributeList(Preferences::ENTITY_TYPE, Table::ACTION_EDIT);

        if (in_array('dashboardLayout', $forbiddenAttributeList)) {
            throw new Forbidden();
        }

        $dashboardLayout = $this->config->get('dashboardLayout');
        $dashletsOptions = $this->config->get('dashletsOptions');

        $preferences->set([
            'dashboardLayout' => $dashboardLayout,
            'dashletsOptions' => $dashletsOptions,
        ]);

        $this->entityManager->saveEntity($preferences);

        return (object) [
            'dashboardLayout' => $preferences->get('dashboardLayout'),
            'dashletsOptions' => $preferences->get('dashletsOptions'),
        ];
    }

    private function getRepository(): Repository
    {
        
        return $this->entityManager->getRepository(Preferences::ENTITY_TYPE);
    }
}

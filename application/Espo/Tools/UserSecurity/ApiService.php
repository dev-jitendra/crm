<?php


namespace Espo\Tools\UserSecurity;

use Espo\Core\Authentication\Logins\Hmac;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Record\ServiceContainer;
use Espo\Core\Utils\Util;
use Espo\Entities\User;
use Espo\ORM\EntityManager;

class ApiService
{
    private ServiceContainer $serviceContainer;
    private User $user;
    private EntityManager $entityManager;

    public function __construct(
        ServiceContainer $serviceContainer,
        User $user,
        EntityManager $entityManager
    ) {
        $this->serviceContainer = $serviceContainer;
        $this->user = $user;
        $this->entityManager = $entityManager;
    }

    
    public function generateNewApiKey(string $id): User
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $service = $this->serviceContainer->get(User::ENTITY_TYPE);

        
        $entity = $service->getEntity($id);

        if (!$entity) {
            throw new NotFound();
        }

        if (!$entity->isApi()) {
            throw new Forbidden();
        }

        $apiKey = Util::generateApiKey();

        $entity->set('apiKey', $apiKey);

        if ($entity->getAuthMethod() === Hmac::NAME) {
            $secretKey = Util::generateSecretKey();

            $entity->set('secretKey', $secretKey);
        }

        $this->entityManager->saveEntity($entity);

        $service->prepareEntityForOutput($entity);

        return $entity;
    }
}

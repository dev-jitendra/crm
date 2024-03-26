<?php


namespace Espo\Services;

use Espo\ORM\Entity;

use Espo\Core\ExternalAccount\Clients\OAuth2Abstract;
use Espo\Core\ExternalAccount\ClientManager;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Exceptions\NotFoundSilent;
use Espo\Core\Exceptions\Forbidden;

use Espo\Core\Record\ReadParams;

use Espo\Core\Di;

use Espo\Entities\ExternalAccount as ExternalAccountEntity;
use Espo\Entities\Integration as IntegrationEntity;

use Exception;


class ExternalAccount extends Record implements Di\HookManagerAware
{
    use Di\HookManagerSetter;

    protected function getClient(string $integration, string $id): ?object
    {
        
        $integrationEntity = $this->entityManager->getEntity('Integration', $integration);

        if (!$integrationEntity) {
            throw new NotFound();
        }

        if (!$integrationEntity->get('enabled')) {
            throw new Error("{$integration} is disabled.");
        }

        $factory = new ClientManager(
            $this->entityManager,
            $this->metadata,
            $this->config,
            $this->injectableFactory
        );

        return $factory->create($integration, $id);
    }

    public function getExternalAccountEntity(string $integration, string $userId): ?ExternalAccountEntity
    {
        
        return $this->entityManager->getEntity('ExternalAccount', $integration . '__' . $userId);
    }

    
    public function ping(string $integration, string $userId)
    {
        try {
            $client = $this->getClient($integration, $userId);

            if ($client && method_exists($client, 'ping')) {
                
                return $client->ping();
            }
        }
        catch (Exception) {}

        return false;
    }

    
    public function authorizationCode(string $integration, string $userId, string $code)
    {
        $entity = $this->getExternalAccountEntity($integration, $userId);

        if (!$entity) {
            throw new NotFound();
        }

        $entity->set('enabled', true);

        $this->entityManager->saveEntity($entity);

        $client = $this->getClient($integration, $userId);

        if ($client instanceof OAuth2Abstract) {
            $result = $client->getAccessTokenFromAuthorizationCode($code);

            if (!empty($result) && !empty($result['accessToken'])) {
                $entity->clear('accessToken');
                $entity->clear('refreshToken');
                $entity->clear('tokenType');
                $entity->clear('expiresAt');

                foreach ($result as $name => $value) {
                    $entity->set($name, $value);
                }

                $this->entityManager->saveEntity($entity);

                $this->hookManager->process('ExternalAccount', 'afterConnect', $entity, [
                    'integration' => $integration,
                    'userId' => $userId,
                    'code' => $code,
                ]);

                return true;
            }
            else {
                throw new Error("Could not get access token for {$integration}.");
            }
        }
        else {
            throw new Error("Could not load client for {$integration}.");
        }
    }

    public function read(string $id, ReadParams $params): Entity
    {
        [, $userId] = explode('__', $id);

        if ($this->user->getId() !== $userId && !$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $entity = $this->entityManager->getEntity('ExternalAccount', $id);

        if (!$entity) {
            throw new NotFoundSilent("Record does not exist.");
        }

        [$integration,] = explode('__', $entity->getId());

        $externalAccountSecretAttributeList = $this->metadata
            ->get(['integrations', $integration, 'externalAccountSecretAttributeList']) ?? [];

        foreach ($externalAccountSecretAttributeList as $a) {
            $entity->clear($a);
        }

        return $entity;
    }
}

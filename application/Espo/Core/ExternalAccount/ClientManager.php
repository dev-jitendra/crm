<?php


namespace Espo\Core\ExternalAccount;

use Espo\Core\Exceptions\Error;

use Espo\Entities\Integration as IntegrationEntity;
use Espo\Entities\ExternalAccount as ExternalAccountEntity;

use Espo\ORM\EntityManager;

use Espo\Core\ExternalAccount\Clients\IClient;
use Espo\Core\ExternalAccount\OAuth2\Client as OAuth2Client;
use Espo\Core\InjectableFactory;
use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;

use Espo\ORM\Entity;

class ClientManager
{
    
    protected $entityManager;

    
    protected $metadata;

    
    protected $config;

    
    protected $injectableFactory = null;

    
    protected $clientMap = [];

    public function __construct(
        EntityManager $entityManager,
        Metadata $metadata,
        Config $config,
        ?InjectableFactory $injectableFactory = null
    ) {
        $this->entityManager = $entityManager;
        $this->metadata = $metadata;
        $this->config = $config;
        $this->injectableFactory = $injectableFactory;
    }

    
    public function storeAccessToken(string $hash, array $data): void
    {
        if (empty($this->clientMap[$hash]) || empty($this->clientMap[$hash]['externalAccountEntity'])) {
            return;
        }

        
        $externalAccountEntity = $this->clientMap[$hash]['externalAccountEntity'];

        $externalAccountEntity->set('accessToken', $data['accessToken']);
        $externalAccountEntity->set('tokenType', $data['tokenType']);
        $externalAccountEntity->set('expiresAt', $data['expiresAt'] ?? null);

        if ($data['refreshToken'] ?? null) {
            $externalAccountEntity->set('refreshToken', $data['refreshToken']);
        }

        $copy = $this->entityManager->getEntity(ExternalAccountEntity::ENTITY_TYPE, $externalAccountEntity->getId());

        if (!$copy) {
            return;
        }

        if (!$copy->get('enabled')) {
            throw new Error("External Account Client Manager: Account got disabled.");
        }

        $copy->set('accessToken', $data['accessToken']);
        $copy->set('tokenType', $data['tokenType']);
        $copy->set('expiresAt', $data['expiresAt'] ?? null);

        if ($data['refreshToken'] ?? null) {
            $copy->set('refreshToken', $data['refreshToken'] ?? null);
        }

        $this->entityManager->saveEntity($copy, [
            'isTokenRenewal' => true,
            SaveOption::SKIP_HOOKS => true,
        ]);
    }

    public function create(string $integration, string $userId): ?object
    {
        $authMethod = $this->metadata->get("integrations.{$integration}.authMethod");

        $methodName = 'create' . ucfirst($authMethod);

        if (method_exists($this, $methodName)) {
            return $this->$methodName($integration, $userId);
        }

        if (!$this->injectableFactory) {
            throw new Error();
        }

        
        $integrationEntity = $this->entityManager->getEntity(IntegrationEntity::ENTITY_TYPE, $integration);

        
        $externalAccountEntity = $this->entityManager
            ->getEntity(ExternalAccountEntity::ENTITY_TYPE, $integration . '__' . $userId);

        if (!$externalAccountEntity) {
            throw new Error("External Account {$integration} not found for {$userId}.");
        }

        if (!$integrationEntity) {
            return null;
        }

        if (!$integrationEntity->get('enabled')) {
            return null;
        }

        if (!$externalAccountEntity->get('enabled')) {
            return null;
        }

        
        $className = $this->metadata->get("integrations.{$integration}.clientClassName");

        $client = $this->injectableFactory->create($className);

        if (!method_exists($client, 'setup')) {
            throw new Error("{$className} does not have `setup` method.");
        }

        $client->setup(
            $userId,
            $integrationEntity,
            $externalAccountEntity,
            $this
        );

        $this->addToClientMap($client, $integrationEntity, $externalAccountEntity, $userId);

        return $client;
    }

    protected function createOAuth2(string $integration, string $userId): ?object
    {
        
        $integrationEntity = $this->entityManager->getEntity(IntegrationEntity::ENTITY_TYPE, $integration);

        
        $externalAccountEntity = $this->entityManager
            ->getEntity(ExternalAccountEntity::ENTITY_TYPE, $integration . '__' . $userId);

        
        $className = $this->metadata->get("integrations.{$integration}.clientClassName");

        $redirectUri = $this->config->get('siteUrl') . '?entryPoint=oauthCallback';

        $redirectUriPath = $this->metadata->get(['integrations', $integration, 'params', 'redirectUriPath']);

        if ($redirectUriPath) {
            $redirectUri = rtrim($this->config->get('siteUrl'), '/') . '/' . $redirectUriPath;
        }

        if (!$externalAccountEntity) {
            throw new Error("External Account {$integration} not found for '{$userId}'.");
        }

        if (!$integrationEntity) {
            return null;
        }

        if (!$integrationEntity->get('enabled')) {
            return null;
        }

        if (!$externalAccountEntity->get('enabled')) {
            return null;
        }

        $oauth2Client = new OAuth2Client();

        $params = [
            'endpoint' => $this->metadata->get("integrations.{$integration}.params.endpoint"),
            'tokenEndpoint' => $this->metadata->get("integrations.{$integration}.params.tokenEndpoint"),
            'clientId' => $integrationEntity->get('clientId'),
            'clientSecret' => $integrationEntity->get('clientSecret'),
            'redirectUri' => $redirectUri,
            'accessToken' => $externalAccountEntity->get('accessToken'),
            'refreshToken' => $externalAccountEntity->get('refreshToken'),
            'tokenType' => $externalAccountEntity->get('tokenType'),
            'expiresAt' => $externalAccountEntity->get('expiresAt'),
        ];

        foreach (get_object_vars($integrationEntity->getValueMap()) as $k => $v) {
            if (array_key_exists($k, $params)) {
                continue;
            }

            if ($integrationEntity->hasAttribute($k)) {
                continue;
            }

            $params[$k] = $v;
        }

        $client = new $className($oauth2Client, $params, $this);

        if ($this->injectableFactory) {
            $this->injectableFactory->createWith($className, [
                'client' => $oauth2Client,
                'params' => $params,
                'manager' => $this,
            ]);
        }
        else {
            
            $client = new $className($oauth2Client, $params, $this);
        }

        $this->addToClientMap($client, $integrationEntity, $externalAccountEntity, $userId);

        return $client;
    }

    
    protected function addToClientMap(
        $client,
        IntegrationEntity $integrationEntity,
        ExternalAccountEntity $externalAccountEntity,
        string $userId
    ) {
        $this->clientMap[spl_object_hash($client)] = [
            'client' => $client,
            'userId' => $userId,
            'integration' => $integrationEntity->getId(),
            'integrationEntity' => $integrationEntity,
            'externalAccountEntity' => $externalAccountEntity,
        ];
    }

    
    protected function getClientRecord($client): Entity
    {
        $data = $this->clientMap[spl_object_hash($client)];

        if (!$data) {
            throw new Error("External Account Client Manager: Client not found in hash.");
        }

        return $data['externalAccountEntity'];
    }

    
    public function isClientLocked($client): bool
    {
        $externalAccountEntity = $this->getClientRecord($client);

        $id = $externalAccountEntity->getId();

        $e = $this->entityManager
            ->getRDBRepository(ExternalAccountEntity::ENTITY_TYPE)
            ->select(['id', 'isLocked'])
            ->where(['id' => $id])
            ->findOne();

        if (!$e) {
            throw new Error("External Account Client Manager: Client '{$id}' not found in DB.");
        }

        return $e->get('isLocked');
    }

    public function lockClient(object $client): void
    {
        $externalAccountEntity = $this->getClientRecord($client);

        $id = $externalAccountEntity->getId();

        $e = $this->entityManager
            ->getRDBRepository(ExternalAccountEntity::ENTITY_TYPE)
            ->select(['id', 'isLocked'])
            ->where(['id' => $id])
            ->findOne();

        if (!$e) {
            throw new Error("External Account Client Manager: Client '{$id}' not found in DB.");
        }

        $e->set('isLocked', true);

        $this->entityManager->saveEntity($e, [
            SaveOption::SKIP_HOOKS => true,
            SaveOption::SILENT => true,
        ]);
    }

    public function unlockClient(object $client): void
    {
        $externalAccountEntity = $this->getClientRecord($client);

        $id = $externalAccountEntity->getId();

        $e = $this->entityManager
            ->getRDBRepository(ExternalAccountEntity::ENTITY_TYPE)
            ->select(['id', 'isLocked'])
            ->where(['id' => $id])
            ->findOne();

        if (!$e) {
            throw new Error("External Account Client Manager: Client '{$id}' not found in DB.");
        }

        $e->set('isLocked', false);

        $this->entityManager->saveEntity($e, [
            SaveOption::SKIP_HOOKS => true,
            SaveOption::SILENT => true,
        ]);
    }

    
    public function reFetchClient(object $client): void
    {
        $externalAccountEntity = $this->getClientRecord($client);

        $id = $externalAccountEntity->getId();

        $e = $this->entityManager->getEntityById(ExternalAccountEntity::ENTITY_TYPE, $id);

        if (!$e) {
            throw new Error("External Account Client Manager: Client {$id} not found in DB.");
        }

        $data = $e->getValueMap();

        $externalAccountEntity->set($data);

        $client->setParams(get_object_vars($data));
    }
}

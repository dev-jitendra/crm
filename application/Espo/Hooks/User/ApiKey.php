<?php


namespace Espo\Hooks\User;

use Espo\ORM\Entity;

use Espo\Core\Utils\ApiKey as ApiKeyUtil;

use Espo\Entities\User;

class ApiKey
{
    private ApiKeyUtil $apiKey;

    public function __construct(ApiKeyUtil $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    
    public function afterSave(Entity $entity): void
    {
        if (!$entity->isApi()) {
            return;
        }

        if (
            $entity->get('apiKey') && $entity->get('secretKey') &&
            (
                $entity->isAttributeChanged('apiKey') ||
                $entity->isAttributeChanged('authMethod')
            )
        ) {
            $this->apiKey->storeSecretKeyForUserId($entity->getId(), $entity->get('secretKey'));
        }

        if (
            $entity->isAttributeChanged('authMethod') &&
            $entity->get('authMethod') !== 'Hmac'
        ) {
            $this->apiKey->removeSecretKeyForUserId($entity->getId());
        }
    }

    
    public function afterRemove(Entity $entity): void
    {
        if (!$entity->isApi()) {
            return;
        }

        if ($entity->get('authMethod') === 'Hmac') {
            $this->apiKey->removeSecretKeyForUserId($entity->getId());
        }
    }
}

<?php


namespace Espo\Core\Authentication\Helper;

use Espo\Core\Authentication\Logins\ApiKey;
use Espo\Core\Authentication\Logins\Hmac;
use Espo\ORM\EntityManager;

use Espo\Entities\User;

class UserFinder
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function find(string $username, string $hash): ?User
    {
        
        $user = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->where([
                'userName' => $username,
                'password' => $hash,
                'type!=' => [User::TYPE_API, User::TYPE_SYSTEM],
            ])
            ->findOne();

        return $user;
    }

    public function findApiHmac(string $apiKey): ?User
    {
        
        $user = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->where([
                'type' => User::TYPE_API,
                'apiKey' => $apiKey,
                'authMethod' => Hmac::NAME,
            ])
            ->findOne();

        return $user;
    }

    public function findApiApiKey(string $apiKey): ?User
    {
        
        $user = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->where([
                'type' => User::TYPE_API,
                'apiKey' => $apiKey,
                'authMethod' => ApiKey::NAME,
            ])
            ->findOne();

        return $user;
    }
}

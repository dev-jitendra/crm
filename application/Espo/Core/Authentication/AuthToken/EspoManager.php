<?php


namespace Espo\Core\Authentication\AuthToken;

use Espo\ORM\EntityManager;
use Espo\ORM\Repository\RDBRepository;
use Espo\Entities\AuthToken as AuthTokenEntity;

use RuntimeException;


class EspoManager implements Manager
{
    
    private RDBRepository $repository;

    private const TOKEN_RANDOM_LENGTH = 16;

    public function __construct(EntityManager $entityManager)
    {
        $this->repository = $entityManager->getRDBRepositoryByClass(AuthTokenEntity::class);
    }

    public function get(string $token): ?AuthToken
    {
        
        $authToken = $this->repository
            ->select([
                'id',
                'isActive',
                'token',
                'secret',
                'userId',
                'portalId',
                'hash',
                'createdAt',
                'lastAccess',
                'modifiedAt',
            ])
            ->where(['token' => $token])
            ->findOne();

        return $authToken;
    }

    public function create(Data $data): AuthToken
    {
        
        $authToken = $this->repository->getNew();

        $authToken
            ->setUserId($data->getUserId())
            ->setPortalId($data->getPortalId())
            ->setHash($data->getHash())
            ->setIpAddress($data->getIpAddress())
            ->setToken($this->generateToken())
            ->setLastAccessNow();

        if ($data->toCreateSecret()) {
            $authToken->setSecret($this->generateToken());
        }

        $this->validate($authToken);

        $this->repository->save($authToken);

        return $authToken;
    }

    public function inactivate(AuthToken $authToken): void
    {
        if (!$authToken instanceof AuthTokenEntity) {
            throw new RuntimeException();
        }

        $this->validateNotChanged($authToken);

        $authToken->setIsActive(false);

        $this->repository->save($authToken);
    }

    public function renew(AuthToken $authToken): void
    {
        if (!$authToken instanceof AuthTokenEntity) {
            throw new RuntimeException();
        }

        $this->validateNotChanged($authToken);

        if ($authToken->isNew()) {
            throw new RuntimeException("Can renew only not new auth token.");
        }

        $authToken->setLastAccessNow();

        $this->repository->save($authToken);
    }

    private function validate(AuthToken $authToken): void
    {
        if (!$authToken->getToken()) {
            throw new RuntimeException("Empty token.");
        }

        if (!$authToken->getUserId()) {
            throw new RuntimeException("Empty user ID.");
        }
    }

    private function validateNotChanged(AuthTokenEntity $authToken): void
    {
        if (
            $authToken->isAttributeChanged('token') ||
            $authToken->isAttributeChanged('secret') ||
            $authToken->isAttributeChanged('hash') ||
            $authToken->isAttributeChanged('userId') ||
            $authToken->isAttributeChanged('portalId')
        ) {
            throw new RuntimeException("Auth token was changed.");
        }
    }

    private function generateToken(): string
    {
        $length = self::TOKEN_RANDOM_LENGTH;

        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($length));
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            
            $randomValue = openssl_random_pseudo_bytes($length);

            return bin2hex($randomValue);
        }

        throw new RuntimeException("Could not generate token.");
    }
}

<?php


namespace Espo\Core\Console\Commands;

use Espo\Entities\User;
use Espo\Core\Authentication\AuthToken\Manager as AuthTokenManager;
use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Core\ORM\EntityManager;

class AuthTokenCheck implements Command
{
    private EntityManager $entityManager;
    private AuthTokenManager $authTokenManager;

    public function __construct(EntityManager $entityManager, AuthTokenManager $authTokenManager)
    {
        $this->entityManager = $entityManager;
        $this->authTokenManager = $authTokenManager;
    }

    public function run(Params $params, IO $io): void
    {
        $token = $params->getArgument(0);

        if (empty($token)) {
            return;
        }

        $authToken = $this->authTokenManager->get($token);

        if (!$authToken) {
            return;
        }

        if (!$authToken->isActive()) {
            return;
        }

        if (!$authToken->getUserId()) {
            return;
        }

        $userId = $authToken->getUserId();

        $user = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->select('id')
            ->where([
                'id' => $userId,
                'isActive' => true,
            ])
            ->findOne();

        if (!$user) {
            return;
        }

        $io->write($user->getId());
    }
}

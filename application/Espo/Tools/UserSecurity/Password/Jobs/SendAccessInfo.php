<?php


namespace Espo\Tools\UserSecurity\Password\Jobs;

use Espo\Core\Mail\Exceptions\SendingError;
use Espo\Entities\User;
use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;
use Espo\Core\Exceptions\Error;
use Espo\ORM\EntityManager;
use Espo\Tools\UserSecurity\Password\Service as PasswordService;

class SendAccessInfo implements Job
{
    private EntityManager $entityManager;
    private PasswordService $passwordService;

    public function __construct(EntityManager $entityManager, PasswordService $passwordService)
    {
        $this->entityManager = $entityManager;
        $this->passwordService = $passwordService;
    }

    
    public function run(Data $data): void
    {
        $userId = $data->getTargetId();

        if (!$userId) {
            throw new Error();
        }

        
        $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $userId);

        if (!$user) {
            throw new Error("User '{$userId}' not found.");
        }

        $this->passwordService->sendAccessInfoForNewUser($user);
    }
}

<?php


namespace Espo\Core;

use Espo\Core\Utils\SystemUser;
use Espo\Entities\User;
use Espo\Core\ORM\EntityManagerProxy;

use RuntimeException;


class ApplicationUser
{
    
    public const SYSTEM_USER_ID = 'system';

    public function __construct(
        private Container $container,
        private EntityManagerProxy $entityManagerProxy
    ) {}

    
    public function setupSystemUser(): void
    {
        $user = $this->entityManagerProxy
            ->getRDBRepository(User::ENTITY_TYPE)
            ->select([
                'id',
                'name',
                'userName',
                'type',
                'isActive',
                'firstName',
                'lastName',
                'deleted',
            ])
            ->where(['userName' => SystemUser::NAME])
            ->findOne();

        if (!$user) {
            throw new RuntimeException("System user is not found.");
        }

        $user->set('ipAddress', $_SERVER['REMOTE_ADDR'] ?? null);
        $user->set('type', User::TYPE_SYSTEM);

        $this->container->set('user', $user);
    }

    
    public function setUser(User $user): void
    {
        $this->container->set('user', $user);
    }
}

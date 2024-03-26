<?php


namespace Espo\Core\Authentication\Oidc\UserProvider;

use Espo\Core\ApplicationState;
use Espo\Core\Authentication\Jwt\Token\Payload;
use Espo\Core\Authentication\Oidc\ConfigDataProvider;
use Espo\Core\Authentication\Oidc\UserProvider;
use Espo\Core\Utils\Log;
use Espo\Entities\User;
use RuntimeException;

class DefaultUserProvider implements UserProvider
{
    public function __construct(
        private ConfigDataProvider $configDataProvider,
        private Sync $sync,
        private UserRepository $userRepository,
        private ApplicationState $applicationState,
        private Log $log
    ) {}

    public function get(Payload $payload): ?User
    {
        $user = $this->findUser($payload);

        if ($user) {
            $this->syncUser($user, $payload);

            return $user;
        }

        return $this->tryToCreateUser($payload);
    }

    private function findUser(Payload $payload): ?User
    {
        $usernameClaim = $this->configDataProvider->getUsernameClaim();

        if (!$usernameClaim) {
            throw new RuntimeException("No username claim in config.");
        }

        $username = $payload->get($usernameClaim);

        if (!$username) {
            throw new RuntimeException("No username claim `{$usernameClaim}` in token.");
        }

        $username = $this->sync->normalizeUsername($username);

        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            return null;
        }

        if (!$user->isActive()) {
            return null;
        }

        $userId = $user->getId();

        $isPortal = $this->applicationState->isPortal();

        if (!$isPortal && !$user->isRegular() && !$user->isAdmin()) {
            $this->log->info("Oidc: User {$userId} found but it's neither regular user not admin.");

            return null;
        }

        if ($isPortal && !$user->isPortal()) {
            $this->log->info("Oidc: User {$userId} found but it's not portal user.");

            return null;
        }

        if ($isPortal) {
            $portalId = $this->applicationState->getPortalId();

            if (!$user->getPortals()->hasId($portalId)) {
                $this->log->info("Oidc: User {$userId} found but it's not related to current portal.");

                return null;
            }
        }

        if ($user->isSuperAdmin()) {
            $this->log->info("Oidc: User {$userId} found but it's super-admin, not allowed.");

            return null;
        }

        if ($user->isAdmin() && !$this->configDataProvider->allowAdminUser()) {
            $this->log->info("Oidc: User {$userId} found but it's admin, not allowed.");

            return null;
        }

        return $user;
    }

    private function tryToCreateUser(Payload $payload): ?User
    {
        if (!$this->configDataProvider->createUser()) {
            return null;
        }

        $usernameClaim = $this->configDataProvider->getUsernameClaim();

        if (!$usernameClaim) {
            throw new RuntimeException("Could not create a user. No OIDC username claim in config.");
        }

        $username = $payload->get($usernameClaim);

        if (!$username) {
            throw new RuntimeException("Could not create a user. No username claim returned in token.");
        }

        return $this->sync->createUser($payload);
    }

    private function syncUser(User $user, Payload $payload): void
    {
        if (
            !$this->configDataProvider->sync() &&
            !$this->configDataProvider->syncTeams()
        ) {
            return;
        }

        $this->sync->syncUser($user, $payload);
    }
}

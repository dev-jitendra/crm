<?php


namespace Espo\Core\Select\Helpers;

use Espo\Entities\User;
use Espo\ORM\EntityManager;
use Espo\Entities\Preferences;
use Espo\Core\Utils\Config;

class UserTimeZoneProvider
{
    public function __construct(
        private User $user,
        private EntityManager $entityManager,
        private Config $config
    ) {}

    public function get(): string
    {
        $preferences = $this->entityManager->getEntity(Preferences::ENTITY_TYPE, $this->user->getId());

        if (!$preferences) {
            return 'UTC';
        }

        if ($preferences->get('timeZone') === null || $preferences->get('timeZone') === '') {
            return $this->config->get('timeZone');
        }

        return $preferences->get('timeZone');
    }
}

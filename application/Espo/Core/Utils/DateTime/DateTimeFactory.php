<?php


namespace Espo\Core\Utils\DateTime;

use Espo\Core\Utils\DateTime;
use Espo\Core\InjectableFactory;
use Espo\ORM\EntityManager;
use Espo\Core\Utils\Config;
use Espo\Entities\User;
use Espo\Entities\Preferences;

class DateTimeFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private EntityManager $entityManager,
        private Config $config
    ) {}

    public function createWithUserTimeZone(User $user): DateTime
    {
        $preferences = $this->entityManager->getEntity(Preferences::ENTITY_TYPE, $user->getId());

        $timeZone = $this->config->get('timeZone') ?? 'UTC';

        if ($preferences) {
            $timeZone = $preferences->get('timeZone') ? $preferences->get('timeZone') : $timeZone;
        }

        return $this->createWithTimeZone($timeZone);
    }

    public function createWithTimeZone(string $timeZone): DateTime
    {
        return $this->injectableFactory->createWith(DateTime::class, [
            'timeZone' => $timeZone,
            'dateFormat' => $this->config->get('dateFormat'),
            'timeFormat' => $this->config->get('timeFormat'),
            'language' => $this->config->get('language'),
        ]);
    }
}

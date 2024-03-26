<?php


namespace Espo\Classes\Jobs;

use Espo\Entities\AuthToken;
use Espo\Core\Job\JobDataLess;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DateTime as DateTimeUtil;

use DateTime;

class AuthTokenControl implements JobDataLess
{
    private Config $config;
    private EntityManager $entityManager;

    public function __construct(Config $config, EntityManager $entityManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function run(): void
    {
        $authTokenLifetime = $this->config->get('authTokenLifetime');
        $authTokenMaxIdleTime = $this->config->get('authTokenMaxIdleTime');

        if (!$authTokenLifetime && !$authTokenMaxIdleTime) {
            return;
        }

        $whereClause = [
            'isActive' => true,
        ];

        if ($authTokenLifetime) {
            $dt = new DateTime();

            $dt->modify('-' . $authTokenLifetime . ' hours');

            $authTokenLifetimeThreshold = $dt->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);

            $whereClause['createdAt<'] = $authTokenLifetimeThreshold;
        }

        if ($authTokenMaxIdleTime) {
            $dt = new DateTime();

            $dt->modify('-' . $authTokenMaxIdleTime . ' hours');

            $authTokenMaxIdleTimeThreshold = $dt->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);

            $whereClause['lastAccess<'] = $authTokenMaxIdleTimeThreshold;
        }

        $tokenList = $this->entityManager
            ->getRDBRepository(AuthToken::ENTITY_TYPE)
            ->where($whereClause)
            ->limit(0, 500)
            ->find();

        foreach ($tokenList as $token) {
            $token->set('isActive', false);

            $this->entityManager->saveEntity($token);
        }
    }
}

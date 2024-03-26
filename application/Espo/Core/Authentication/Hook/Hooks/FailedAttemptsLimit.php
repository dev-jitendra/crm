<?php


namespace Espo\Core\Authentication\Hook\Hooks;

use Espo\Core\Api\Util;
use Espo\Core\Authentication\Hook\BeforeLogin;
use Espo\Core\Authentication\AuthenticationData;
use Espo\Core\Api\Request;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Authentication\ConfigDataProvider;
use Espo\Core\Utils\Log;
use Espo\ORM\EntityManager;
use Espo\Entities\AuthLogRecord;

use DateTime;

class FailedAttemptsLimit implements BeforeLogin
{
    public function __construct(
        private ConfigDataProvider $configDataProvider,
        private EntityManager $entityManager,
        private Log $log,
        private Util $util
    ) {}

    
    public function process(AuthenticationData $data, Request $request): void
    {
        $isByTokenOnly = !$data->getMethod() && $request->getHeader('Espo-Authorization-By-Token') === 'true';

        if ($isByTokenOnly) {
            return;
        }

        if ($this->configDataProvider->isAuthLogDisabled()) {
            return;
        }

        $failedAttemptsPeriod = $this->configDataProvider->getFailedAttemptsPeriod();
        $maxFailedAttempts = $this->configDataProvider->getMaxFailedAttemptNumber();

        $requestTime = intval($request->getServerParam('REQUEST_TIME_FLOAT'));

        $requestTimeFrom = (new DateTime('@' . $requestTime))->modify('-' . $failedAttemptsPeriod);

        $ip = $this->util->obtainIpFromRequest($request);

        $where = [
            'requestTime>' => $requestTimeFrom->format('U'),
            'ipAddress' => $ip,
            'isDenied' => true,
        ];

        $wasFailed = (bool) $this->entityManager
            ->getRDBRepository(AuthLogRecord::ENTITY_TYPE)
            ->select(['id'])
            ->where($where)
            ->findOne();

        if (!$wasFailed) {
            return;
        }

        $failAttemptCount = $this->entityManager
            ->getRDBRepository(AuthLogRecord::ENTITY_TYPE)
            ->where($where)
            ->count();

        if ($failAttemptCount <= $maxFailedAttempts) {
            return;
        }

        $this->log->warning("AUTH: Max failed login attempts exceeded for IP '{$ip}'.");

        throw new Forbidden("Max failed login attempts exceeded.");
    }
}

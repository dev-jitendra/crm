<?php


namespace Espo\Classes\Cleanup;

use Espo\Core\Cleanup\Cleanup;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DateTime as DateTimeUtil;

use Espo\ORM\EntityManager;

use Espo\Entities\TwoFactorCode;

use DateTime;

class TwoFactorCodes implements Cleanup
{
    private const PERIOD = '5 days';

    private $config;

    private $entityManager;

    public function __construct(Config $config, EntityManager $entityManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function process(): void
    {
        $period = '-' . $this->config->get('cleanupTwoFactorCodesPeriod', self::PERIOD);

        $from = (new DateTime())
            ->modify($period)
            ->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);

        $query = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from(TwoFactorCode::ENTITY_TYPE)
            ->where([
                'createdAt<' => $from,
            ])
            ->build();

        $this->entityManager
            ->getQueryExecutor()
            ->execute($query);
    }
}

<?php


namespace Espo\Classes\Cleanup;

use Espo\Core\Cleanup\Cleanup;
use Espo\Core\Utils\Config;
use Espo\ORM\EntityManager;

use Espo\Core\Field\DateTime;

class MassActions implements Cleanup
{
    private $config;

    private $entityManager;

    private string $cleanupPeriod = '14 days';

    public function __construct(Config $config, EntityManager $entityManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function process(): void
    {
        $period = '-' . $this->config->get('cleanupMassActionsPeriod', $this->cleanupPeriod);

        $before = DateTime::createNow()
            ->modify($period)
            ->toString();

        $delete = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from('MassAction')
            ->where([
                'createdAt<' => $before,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($delete);
    }
}

<?php


namespace Espo\Classes\Cleanup;

use Espo\Core\Cleanup\Cleanup;
use Espo\Core\Utils\Config;
use Espo\Core\Field\DateTime;

use Espo\ORM\EntityManager;

use Espo\Entities\PasswordChangeRequest;

class PasswordChangeRequests implements Cleanup
{
    private Config $config;
    private EntityManager $entityManager;

    private string $cleanupPeriod = '30 days';

    public function __construct(Config $config, EntityManager $entityManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function process(): void
    {
        $period = '-' . $this->config->get('cleanupPasswordChangeRequestsPeriod', $this->cleanupPeriod);

        $before = DateTime::createNow()
            ->modify($period)
            ->toString();

        $delete = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from(PasswordChangeRequest::ENTITY_TYPE)
            ->where([
                'createdAt<' => $before,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($delete);
    }
}

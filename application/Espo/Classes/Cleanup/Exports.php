<?php


namespace Espo\Classes\Cleanup;

use Espo\Core\Cleanup\Cleanup;
use Espo\Core\Utils\Config;
use Espo\ORM\EntityManager;

use Espo\Core\Field\DateTime;

use Espo\Entities\Export;

class Exports implements Cleanup
{
    private $config;

    private $entityManager;

    private string $cleanupPeriod = '2 days';

    public function __construct(Config $config, EntityManager $entityManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function process(): void
    {
        $period = '-' . $this->config->get('cleanupExportsPeriod', $this->cleanupPeriod);

        $before = DateTime::createNow()
            ->modify($period)
            ->toString();

        $delete = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from(Export::ENTITY_TYPE)
            ->where([
                'createdAt<' => $before,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($delete);
    }
}

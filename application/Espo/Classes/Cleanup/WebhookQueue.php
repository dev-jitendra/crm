<?php


namespace Espo\Classes\Cleanup;

use Espo\Core\Cleanup\Cleanup;
use Espo\Core\Utils\Config;
use Espo\ORM\EntityManager;

use DateTime;

class WebhookQueue implements Cleanup
{
    private string $cleanupWebhookQueuePeriod = '10 days';

    private $config;

    private $entityManager;

    public function __construct(Config $config, EntityManager $entityManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function process(): void
    {
        $period = '-' . $this->config->get('cleanupWebhookQueuePeriod', $this->cleanupWebhookQueuePeriod);

        $datetime = new DateTime();

        $datetime->modify($period);
        $from = $datetime->format('Y-m-d H:i:s');

        $query1 = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from('WebhookQueueItem')
            ->where([
                'DATE:(createdAt)<' => $from,
                'OR' => [
                    'status!=' => 'Pending',
                    'deleted' => true,
                ],
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($query1);

        $query2 = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from('WebhookEventQueueItem')
            ->where([
                'DATE:(createdAt)<' => $from,
                'OR' => [
                    'isProcessed' => true,
                    'deleted' => true,
                ],
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($query2);
    }
}

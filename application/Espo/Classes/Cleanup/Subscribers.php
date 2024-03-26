<?php


namespace Espo\Classes\Cleanup;

use Espo\Core\Cleanup\Cleanup;
use Espo\Core\Field\DateTime;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Entities\Subscription;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\Part\Condition as Cond;

class Subscribers implements Cleanup
{
    private const PERIOD = '2 months';

    private Metadata $metadata;
    private EntityManager $entityManager;
    private Config $config;

    public function __construct(
        Metadata $metadata,
        EntityManager $entityManager,
        Config $config
    ) {
        $this->metadata = $metadata;
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    public function process(): void
    {
        if (!$this->config->get('cleanupSubscribers')) {
            return;
        }

        
        $scopeList = array_keys($this->metadata->get(['scopes']) ?? []);

        
        $scopeList = array_values(array_filter(
            $scopeList,
            fn ($item) => (bool) $this->metadata->get(['scopes', $item, 'stream'])
        ));

        foreach ($scopeList as $scope) {
            $this->processEntityType($scope);
        }
    }

    private function processEntityType(string $entityType): void
    {
        
        $data = $this->metadata->get(['streamDefs', $entityType, 'subscribersCleanup']);

        if (!($data['enabled'] ?? false)) {
            return;
        }

        
        $dateField = $data['dateField'] ?? 'createdAt';
        
        $statusList = $data['statusList'] ?? null;
        
        $statusField = $this->metadata->get(['scopes', $entityType, 'statusField']);

        if ($statusList === null || $statusField === null) {
            return;
        }

        
        $period = $this->metadata->get(['streamDefs', $entityType, 'subscribersCleanup', 'period']) ??
            $this->config->get('cleanupSubscribersPeriod') ??
            self::PERIOD;

        $before = DateTime::createNow()->modify('-' . $period);

        $query = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from(Subscription::ENTITY_TYPE, 'subscription')
            ->join(
                $entityType,
                'entity',
                Cond::equal(
                    Cond::column('entity.id'),
                    Cond::column('entityId')
                )
            )
            ->where(
                Cond::and(
                    Cond::equal(
                        Cond::column('entityType'),
                        $entityType
                    ),
                    Cond::less(
                        Cond::column('entity.' . $dateField),
                        $before->toString()
                    ),
                    Cond::in(
                        Cond::column('entity.' . $statusField),
                        $statusList
                    )
                )
            )
            ->build();

        $this->entityManager->getQueryExecutor()->execute($query);
    }
}

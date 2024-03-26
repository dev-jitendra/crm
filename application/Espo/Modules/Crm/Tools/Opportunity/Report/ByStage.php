<?php


namespace Espo\Modules\Crm\Tools\Opportunity\Report;

use Espo\Core\Acl;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Select\SelectBuilderFactory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Modules\Crm\Entities\Opportunity;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\Order;
use stdClass;

class ByStage
{
    private Acl $acl;
    private Config $config;
    private Metadata $metadata;
    private EntityManager $entityManager;
    private SelectBuilderFactory $selectBuilderFactory;
    private Util $util;

    public function __construct(
        Acl $acl,
        Config $config,
        Metadata $metadata,
        EntityManager $entityManager,
        SelectBuilderFactory $selectBuilderFactory,
        Util $util
    ) {
        $this->acl = $acl;
        $this->config = $config;
        $this->metadata = $metadata;
        $this->entityManager = $entityManager;
        $this->selectBuilderFactory = $selectBuilderFactory;
        $this->util = $util;
    }

    
    public function run(DateRange $range): stdClass
    {
        $range = $range->withFiscalYearShift(
            $this->config->get('fiscalYearShift') ?? 0
        );

        if (!$this->acl->checkScope(Opportunity::ENTITY_TYPE, Acl\Table::ACTION_READ)) {
            throw new Forbidden();
        }

        if (in_array('amount', $this->acl->getScopeForbiddenAttributeList(Opportunity::ENTITY_TYPE))) {
            throw new Forbidden();
        }

        [$from, $to] = $range->getRange();

        $options = $this->metadata->get('entityDefs.Opportunity.fields.stage.options') ?? [];

        $queryBuilder = $this->selectBuilderFactory
            ->create()
            ->from(Opportunity::ENTITY_TYPE)
            ->withStrictAccessControl()
            ->buildQueryBuilder();

        $whereClause = [
            ['stage!=' => $this->util->getLostStageList()],
            ['stage!=' => $this->util->getWonStageList()],
        ];

        if ($from && $to) {
            $whereClause[] = [
                'closeDate>=' => $from->toString(),
                'closeDate<' => $to->toString(),
            ];
        }

        if ($from && !$to) {
            $whereClause[] = [
                'closeDate>=' => $from->toString(),
            ];
        }

        if (!$from && $to) {
            $whereClause[] = [
                'closeDate<' => $to->toString(),
            ];
        }
        $queryBuilder
            ->select([
                'stage',
                ['SUM:amountConverted', 'amount'],
            ])
            ->order(
                Order::createByPositionInList(
                    Expression::column('stage'),
                    $options
                )
            )
            ->group('stage')
            ->where($whereClause);

        $stageIgnoreList = array_merge(
            $this->util->getLostStageList(),
            $this->util->getWonStageList()
        );

        $this->util->handleDistinctReportQueryBuilder($queryBuilder, $whereClause);

        $sth = $this->entityManager
            ->getQueryExecutor()
            ->execute($queryBuilder->build());

        $rowList = $sth->fetchAll() ?: [];

        $result = [];

        foreach ($rowList as $row) {
            $stage = $row['stage'];

            if (in_array($stage, $stageIgnoreList)) {
                continue;
            }

            $result[$stage] = floatval($row['amount']);
        }

        foreach ($options as $stage) {
            if (in_array($stage, $stageIgnoreList)) {
                continue;
            }

            if (array_key_exists($stage, $result)) {
                continue;
            }

            $result[$stage] = 0.0;
        }

        return (object) $result;
    }
}

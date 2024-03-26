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

class SalesPipeline
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

    
    public function run(DateRange $range, bool $useLastStage = false, ?string $teamId = null): stdClass
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

        $lostStageList = $this->util->getLostStageList();

        $options = $this->metadata->get('entityDefs.Opportunity.fields.stage.options', []);

        $queryBuilder = $this->selectBuilderFactory
            ->create()
            ->from(Opportunity::ENTITY_TYPE)
            ->withStrictAccessControl()
            ->buildQueryBuilder();

        $stageField = 'stage';

        if ($useLastStage) {
            $stageField = 'lastStage';
        }

        $whereClause = [
            [$stageField . '!=' => $lostStageList],
            [$stageField . '!=' => null],
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

        if ($teamId) {
            $whereClause[] = [
                'teamsFilter.id' => $teamId,
            ];
        }

        $queryBuilder
            ->select([
                $stageField,
                ['SUM:amountConverted', 'amount'],
            ])
            ->order(
                Order::createByPositionInList(
                    Expression::column($stageField),
                    $options
                )
            )
            ->group($stageField)
            ->where($whereClause);

        if ($teamId) {
            $queryBuilder->join('teams', 'teamsFilter');
        }

        $this->util->handleDistinctReportQueryBuilder($queryBuilder, $whereClause);

        $sth = $this->entityManager
            ->getQueryExecutor()
            ->execute($queryBuilder->build());

        $rowList = $sth->fetchAll() ?: [];

        $data = [];

        foreach ($rowList as $row) {
            $stage = $row[$stageField];

            $data[$stage] = floatval($row['amount']);
        }

        $dataList = [];

        $stageList = $this->metadata->get('entityDefs.Opportunity.fields.stage.options', []);

        foreach ($stageList as $stage) {
            if (in_array($stage, $lostStageList)) {
                continue;
            }

            if (!in_array($stage, $lostStageList) && !isset($data[$stage])) {
                $data[$stage] = 0.0;
            }

            $dataList[] = [
                'stage' => $stage,
                'value' => $data[$stage],
            ];
        }

        return (object) [
            'dataList' => $dataList,
        ];
    }
}

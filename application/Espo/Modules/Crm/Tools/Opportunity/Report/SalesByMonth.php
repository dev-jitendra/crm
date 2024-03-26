<?php


namespace Espo\Modules\Crm\Tools\Opportunity\Report;

use DateTime;
use Espo\Core\Acl;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Select\SelectBuilderFactory;
use Espo\Core\Utils\Config;
use Espo\Modules\Crm\Entities\Opportunity;
use Espo\ORM\EntityManager;
use Exception;
use InvalidArgumentException;
use LogicException;
use stdClass;

class SalesByMonth
{
    private Acl $acl;
    private Config $config;
    private EntityManager $entityManager;
    private SelectBuilderFactory $selectBuilderFactory;
    private Util $util;

    public function __construct(
        Acl $acl,
        Config $config,
        EntityManager $entityManager,
        SelectBuilderFactory $selectBuilderFactory,
        Util $util
    ) {
        $this->acl = $acl;
        $this->config = $config;
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

        if (!$from || !$to) {
            throw new InvalidArgumentException();
        }

        $queryBuilder = $this->selectBuilderFactory
            ->create()
            ->from(Opportunity::ENTITY_TYPE)
            ->withStrictAccessControl()
            ->buildQueryBuilder();

        $whereClause = [
            'stage' => $this->util->getWonStageList(),
        ];

        $whereClause[] = [
            'closeDate>=' => $from->toString(),
            'closeDate<' => $to->toString(),
        ];

        $queryBuilder
            ->select([
                ['MONTH:closeDate', 'month'],
                ['SUM:amountConverted', 'amount'],
            ])
            ->order('MONTH:closeDate')
            ->group('MONTH:closeDate')
            ->where($whereClause);

        $this->util->handleDistinctReportQueryBuilder($queryBuilder, $whereClause);

        $sth = $this->entityManager
            ->getQueryExecutor()
            ->execute($queryBuilder->build());

        $result = [];

        $rowList = $sth->fetchAll() ?: [];

        foreach ($rowList as $row) {
            $month = $row['month'];

            $result[$month] = floatval($row['amount']);
        }

        $dt = $from;
        $dtTo = $to;

        if ($dtTo->getDay() > 1) {
            $dtTo = $dtTo
                ->addDays(1 - $dtTo->getDay()) 
                ->addMonths(1);
        }
        else {
            $dtTo = $dtTo->addDays(1 - $dtTo->getDay());
        }

        while ($dt->toTimestamp() < $dtTo->toTimestamp()) {
            $month = $dt->toDateTime()->format('Y-m');

            if (!array_key_exists($month, $result)) {
                $result[$month] = 0;
            }

            $dt = $dt->addMonths(1);
        }

        $keyList = array_keys($result);

        sort($keyList);

        $today = new DateTime();
        $endPosition = count($keyList);

        for ($i = count($keyList) - 1; $i >= 0; $i--) {
            $key = $keyList[$i];

            try {
                $dt = new DateTime($key . '-01');
            }
            catch (Exception $e) {
                throw new LogicException();
            }

            if ($dt->getTimestamp() < $today->getTimestamp()) {
                break;
            }

            if (empty($result[$key])) {
                $endPosition = $i;

                continue;
            }

            break;
        }

        $keyListSliced = array_slice($keyList, 0, $endPosition);

        return (object) [
            'keyList' => $keyListSliced,
            'dataMap' => $result,
        ];
    }
}

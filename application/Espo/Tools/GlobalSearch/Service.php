<?php


namespace Espo\Tools\GlobalSearch;

use Espo\Core\Acl;
use Espo\Core\Record\Collection;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;
use Espo\ORM\EntityCollection;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\Select;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Query\Part\Expression as Expr;

use Espo\Core\Select\Text\FullTextSearch\DataComposerFactory as FullTextSearchDataComposerFactory;
use Espo\Core\Select\Text\FullTextSearch\DataComposer\Params as FullTextSearchDataComposerParams;
use Espo\Core\Select\SelectBuilderFactory;

class Service
{
    public function __construct(
        private FullTextSearchDataComposerFactory $fullTextSearchDataComposerFactory,
        private SelectBuilderFactory $selectBuilderFactory,
        private EntityManager $entityManager,
        private Metadata $metadata,
        private Acl $acl,
        private Config $config
    ) {}

    
    public function find(string $filter, int $offset = 0, ?int $maxSize = null): Collection
    {
        $entityTypeList = $this->config->get('globalSearchEntityList') ?? [];
        $maxSize ??= (int) $this->config->get('recordsPerPage');

        $hasFullTextSearch = false;

        $queryList = [];

        foreach ($entityTypeList as $i => $entityType) {
            $query = $this->getEntityTypeQuery(
                $entityType,
                $i,
                $filter,
                $offset,
                $maxSize,
                $hasFullTextSearch
            );

            if (!$query) {
                continue;
            }

            $queryList[] = $query;
        }

        if (count($queryList) === 0) {
            return new Collection(new EntityCollection(), 0);
        }

        $builder = $this->entityManager->getQueryBuilder()
            ->union()
            ->all()
            ->limit($offset, $maxSize + 1);

        foreach ($queryList as $query) {
            $builder->query($query);
        }

        if ($hasFullTextSearch) {
            $builder->order('relevance', 'DESC');
        }

        $builder->order('order', 'DESC');
        $builder->order('name', 'ASC');

        $unionQuery = $builder->build();

        $collection = new EntityCollection();

        $sth = $this->entityManager->getQueryExecutor()->execute($unionQuery);

        while ($row = $sth->fetch()) {
            $entity = $this->entityManager
                ->getRDBRepository($row['entityType'])
                ->select(['id', 'name'])
                ->where(['id' => $row['id']])
                ->findOne();

            if (!$entity) {
                continue;
            }

            $collection->append($entity);
        }

        return Collection::createNoCount($collection, $maxSize);
    }

    protected function getEntityTypeQuery(
        string $entityType,
        int $i,
        string $filter,
        int $offset,
        int $maxSize,
        bool &$hasFullTextSearch
    ): ?Select {

        if (!$this->acl->checkScope($entityType, Acl\Table::ACTION_READ)) {
            return null;
        }

        if (!$this->metadata->get(['scopes', $entityType])) {
            return null;
        }

        $selectBuilder = $this->selectBuilderFactory
            ->create()
            ->from($entityType)
            ->withStrictAccessControl()
            ->withTextFilter($filter);

        $entityDefs = $this->entityManager->getDefs()->getEntity($entityType);

        $nameAttribute = $entityDefs->hasField('name') ?
            'name' : 'id';

        $selectList = [
            'id',
            $nameAttribute,
            ['VALUE:' . $entityType, 'entityType'],
            [(string) $i, 'order'],
        ];

        $fullTextSearchDataComposer = $this->fullTextSearchDataComposerFactory->create($entityType);

        $fullTextSearchData = $fullTextSearchDataComposer->compose(
            $filter,
            FullTextSearchDataComposerParams::create()
        );

        $isPerson = $this->metadata
            ->get(['entityDefs', $entityType, 'fields', 'name', 'type']) === 'personName';

        if ($isPerson) {
            $selectList[] = 'firstName';
            $selectList[] = 'lastName';
        }
        else {
            $selectList[] = ['VALUE:', 'firstName'];
            $selectList[] = ['VALUE:', 'lastName'];
        }

        $query = $selectBuilder->build();

        $queryBuilder = $this->entityManager
            ->getQueryBuilder()
            ->select()
            ->clone($query)
            ->limit(0, $offset + $maxSize + 1)
            ->select($selectList)
            ->order([]);

        if ($fullTextSearchData) {
            $hasFullTextSearch = true;

            $expression = $fullTextSearchData->getExpression();

            $queryBuilder
                ->select($expression, 'relevance')
                ->order($expression, Order::DESC);
        }
        else {
            $queryBuilder->select(Expr::value(1.1), 'relevance');
        }

        $queryBuilder->order($nameAttribute);

        return $queryBuilder->build();
    }
}

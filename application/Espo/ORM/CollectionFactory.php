<?php


namespace Espo\ORM;

use Espo\ORM\Query\Select;


class CollectionFactory
{
    public function __construct(protected EntityManager $entityManager)
    {}

    
    public function create(?string $entityType = null, array $dataList = []): EntityCollection
    {
        return new EntityCollection($dataList, $entityType, $this->entityManager->getEntityFactory());
    }

    
    public function createFromSql(string $entityType, string $sql): SthCollection
    {
        return SthCollection::fromSql($entityType, $sql, $this->entityManager);
    }

    
    public function createFromQuery(Select $query): SthCollection
    {
        return SthCollection::fromQuery($query, $this->entityManager);
    }

    
    public function createFromSthCollection(SthCollection $sthCollection): EntityCollection
    {
        
        return EntityCollection::fromSthCollection($sthCollection);
    }
}

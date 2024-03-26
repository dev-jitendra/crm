<?php


namespace Espo\Core\ORM;

use Espo\ORM\BaseEntity;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Type\RelationType;

use LogicException;
use stdClass;


class Entity extends BaseEntity
{
    
    public function hasLinkMultipleField(string $field): bool
    {
        return
            $this->hasRelation($field) &&
            $this->getAttributeParam($field . 'Ids', 'isLinkMultipleIdList');
    }

    
    public function hasLinkField(string $field): bool
    {
        return $this->hasAttribute($field . 'Id') && $this->hasRelation($field);
    }

    
    public function hasLinkParentField(string $field): bool
    {
        return
            $this->getAttributeType($field . 'Type') == 'foreignType' &&
            $this->hasAttribute($field . 'Id') &&
            $this->hasRelation($field);
    }

    
    public function loadParentNameField(string $field): void
    {
        if (!$this->hasLinkParentField($field)) {
            throw new LogicException("Called `loadParentNameField` on non-link-parent field `$field`.");
        }

        $parentId = $this->get($field . 'Id');
        $parentType = $this->get($field . 'Type');

        if (!$this->entityManager) {
            throw new LogicException("No entity-manager.");
        }

        if (!$parentId || !$parentType) {
            $this->set($field . 'Name', null);

            return;
        }

        if (!$this->entityManager->hasRepository($parentType)) {
            return;
        }

        $repository = $this->entityManager->getRDBRepository($parentType);

        $select = ['id', 'name'];

        $foreignEntity = $repository
            ->select($select)
            ->where(['id' => $parentId])
            ->findOne();

        if ($foreignEntity) {
            $this->set($field . 'Name', $foreignEntity->get('name'));

            return;
        }

        $this->set($field . 'Name', null);
    }

    
    protected function getRelationOrderParams(string $link): ?array
    {
        $field = $link;

        $idsAttribute = $field . 'Ids';

        $foreignEntityType = $this->getRelationParam($field, 'entity');

        if ($this->getAttributeParam($idsAttribute, 'orderBy')) {
            $defs = [
                'orderBy' => $this->getAttributeParam($idsAttribute, 'orderBy'),
                'order' => 'ASC',
            ];

            if ($this->getAttributeParam($idsAttribute, 'orderDirection')) {
                $defs['order'] = $this->getAttributeParam($idsAttribute, 'orderDirection');
            }

            return $defs;
        }

        if (!$foreignEntityType || !$this->entityManager) {
            return null;
        }

        $ormDefs = $this->entityManager->getMetadata()->getDefs();

        if (!$ormDefs->hasEntity($foreignEntityType)) {
            return null;
        }

        $entityDefs = $ormDefs->getEntity($foreignEntityType);

        $collectionDefs = $entityDefs->getParam('collection') ?? [];

        $orderBy = $collectionDefs['orderBy'] ?? null;
        $order = $collectionDefs['order'] ?? 'ASC';

        if (!$orderBy) {
            return null;
        }

        if (!$entityDefs->hasAttribute($orderBy)) {
            return null;
        }

        return [
            'orderBy' => $orderBy,
            'order' => $order,
        ];
    }

    
    public function loadLinkMultipleField(string $field, ?array $columns = null): void
    {
        if (!$this->hasLinkMultipleField($field)) {
            throw new LogicException("Called `loadLinkMultipleField` on non-link-multiple field `$field`.");
        }

        if (!$this->entityManager) {
            throw new LogicException("No entity-manager.");
        }

        $select = ['id', 'name'];

        $hasType = $this->hasAttribute($field . 'Types');

        if ($hasType) {
            $select[] = 'type';
        }

        if (!empty($columns)) {
            foreach ($columns as $item) {
                $select[] = $item;
            }
        }

        $selectBuilder = $this->entityManager
            ->getRDBRepository($this->getEntityType())
            ->getRelation($this, $field)
            ->select($select);

        $orderBy = null;
        $order = null;

        $orderParams = $this->getRelationOrderParams($field);

        if ($orderParams) {
            $orderBy = $orderParams['orderBy'] ?? null;
            
            $order = $orderParams['order'] ?? null;
        }

        if ($orderBy) {
            if (is_string($orderBy) && !in_array($orderBy, $select)) {
                $selectBuilder->select($orderBy);
            }

            if (is_string($order)) {
                $order = strtoupper($order);

                if ($order !== Order::ASC && $order !== Order::DESC) {
                    $order = Order::ASC;
                }
            }

            $selectBuilder->order($orderBy, $order);
        }

        $collection = $selectBuilder->find();

        $ids = [];
        $names = (object) [];
        $types = (object) [];
        $columnsData = (object) [];

        foreach ($collection as $e) {
            $id = $e->getId();

            $ids[] = $id;

            $names->$id = $e->get('name');

            if ($hasType) {
                $types->$id = $e->get('type');
            }

            if (empty($columns)) {
                continue;
            }

            $columnsData->$id = (object) [];

            foreach ($columns as $column => $f) {
                $columnsData->$id->$column = $e->get($f);
            }
        }

        $idsAttribute = $field . 'Ids';

        $this->set($idsAttribute, $ids);

        if (!$this->isNew() && !$this->hasFetched($idsAttribute)) {
            $this->setFetched($idsAttribute, $ids);
        }

        $this->set($field . 'Names', $names);

        if ($hasType) {
            $this->set($field . 'Types', $types);
        }

        if (!empty($columns)) {
            $this->set($field . 'Columns', $columnsData);
        }
    }

    
    public function loadLinkField(string $field): void
    {
        if (!$this->hasLinkField($field)) {
            throw new LogicException("Called `loadLinkField` on non-link field '$field'.");
        }

        if (
            $this->getRelationType($field) !== RelationType::HAS_ONE &&
            $this->getRelationType($field) !== RelationType::BELONGS_TO
        ) {
            throw new LogicException("Can't load link '$field'.");
        }

        if (!$this->entityManager) {
            throw new LogicException("No entity-manager.");
        }

        $select = ['id', 'name'];

        $entity = $this->entityManager
            ->getRDBRepository($this->getEntityType())
            ->getRelation($this, $field)
            ->select($select)
            ->findOne();

        $entityId = null;
        $entityName = null;

        if ($entity) {
            $entityId = $entity->getId();
            $entityName = $entity->get('name');
        }

        $idAttribute = $field . 'Id';

        if (!$this->isNew() && !$this->hasFetched($idAttribute)) {
            $this->setFetched($idAttribute, $entityId);
        }

        $this->set($idAttribute, $entityId);
        $this->set($field . 'Name', $entityName);
    }

    
    public function getLinkMultipleName(string $field, string $id): ?string
    {
        $namesAttribute = $field . 'Names';

        if (!$this->hasAttribute($namesAttribute)) {
            throw new LogicException("Called `getLinkMultipleName` on non-link-multiple field `$field.");
        }

        if (!$this->has($namesAttribute)) {
            return null;
        }

        $object = $this->get($namesAttribute) ?? (object) [];

        if (!$object instanceof stdClass) {
            throw new LogicException("Non-object value in `$namesAttribute`.");
        }

        return $object?->$id ?? null;
    }

    
    public function setLinkMultipleName(string $field, string $id, ?string $value): void
    {
        $namesAttribute = $field . 'Names';

        if (!$this->hasAttribute($namesAttribute)) {
            throw new LogicException("Called `setLinkMultipleName` on non-link-multiple field `$field.");
        }

        if (!$this->has($namesAttribute)) {
            return;
        }

        $object = $this->get($namesAttribute) ?? (object) [];

        if (!$object instanceof stdClass) {
            throw new LogicException("Non-object value in `$namesAttribute`.");
        }

        $object->$id = $value;

        $this->set($namesAttribute, $object);
    }

    
    public function getLinkMultipleColumn(string $field, string $column, string $id): mixed
    {
        $columnsAttribute = $field . 'Columns';

        if (!$this->hasAttribute($columnsAttribute)) {
            throw new LogicException("Called `getLinkMultipleColumn` on not supported field `$field.");
        }

        if (!$this->has($columnsAttribute)) {
            return null;
        }

        $object = $this->get($columnsAttribute) ?? (object) [];

        if (!$object instanceof stdClass) {
            throw new LogicException("Non-object value in `$columnsAttribute`.");
        }

        return $object?->$id?->$column ?? null;
    }

    
    public function setLinkMultipleColumn(string $field, string $column, string $id, mixed $value): void
    {
        $columnsAttribute = $field . 'Columns';

        if (!$this->hasAttribute($columnsAttribute)) {
            throw new LogicException("Called `setLinkMultipleColumn` on non-link-multiple field `$field.");
        }

        $object = $this->get($columnsAttribute) ?? (object) [];

        if (!$object instanceof stdClass) {
            throw new LogicException("Non-object value in `$columnsAttribute`.");
        }

        $object->$id ??= (object) [];
        $object->$id->$column = $value;

        $this->set($columnsAttribute, $object);
    }

    
    public function setLinkMultipleIdList(string $field, array $idList): void
    {
        $idsAttribute = $field . 'Ids';

        if (!$this->hasAttribute($idsAttribute)) {
            throw new LogicException("Called `setLinkMultipleIdList` on non-link-multiple field `$field.");
        }

        $this->set($idsAttribute, $idList);
    }

    
    public function addLinkMultipleId(string $field, string $id): void
    {
        $idsAttribute = $field . 'Ids';

        if (!$this->hasAttribute($idsAttribute)) {
            throw new LogicException("Called `addLinkMultipleId` on non-link-multiple field `$field.");
        }

        if (!$this->has($idsAttribute)) {
            if (!$this->isNew()) {
                $this->loadLinkMultipleField($field);
            } else {
                $this->set($idsAttribute, []);
            }
        }

        if (!$this->has($idsAttribute)) {
            return;
        }

        $idList = $this->get($idsAttribute);

        if ($idList === null) {
            throw new LogicException("Null value set in `$idsAttribute`.");
        }

        if (!is_array($idList)) {
            throw new LogicException("Non-array value set in `$idsAttribute`.");
        }

        if (in_array($id, $idList)) {
            return;
        }

        $idList[] = $id;

        $this->set($idsAttribute, $idList);
    }

    
    public function removeLinkMultipleId(string $field, string $id): void
    {
        if (!$this->hasLinkMultipleId($field, $id)) {
            return;
        }

        $list = $this->getLinkMultipleIdList($field);

        $index = array_search($id, $list);

        if ($index !== false) {
            unset($list[$index]);

            $list = array_values($list);
        }

        $this->setLinkMultipleIdList($field, $list);
    }

    
    public function getLinkMultipleIdList(string $field): array
    {
        $idsAttribute = $field . 'Ids';

        if (!$this->hasAttribute($idsAttribute)) {
            throw new LogicException("Called `getLinkMultipleIdList` for non-link-multiple field `$field.");
        }

        if (!$this->has($idsAttribute)) {
            if (!$this->isNew()) {
                $this->loadLinkMultipleField($field);
            }
        }

        
        return $this->get($idsAttribute) ?? [];
    }

    
    public function hasLinkMultipleId(string $field, string $id): bool
    {
        $idsAttribute = $field . 'Ids';

        if (!$this->hasAttribute($idsAttribute)) {
            throw new LogicException("Called `hasLinkMultipleId` for non-link-multiple field `$field.");
        }

        if (!$this->has($idsAttribute)) {
            if (!$this->isNew()) {
                $this->loadLinkMultipleField($field);
            }
        }

        if (!$this->has($idsAttribute)) {
            return false;
        }

        
        $idList = $this->get($idsAttribute) ?? [];

        return in_array($id, $idList);
    }
}

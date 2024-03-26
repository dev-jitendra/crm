<?php


namespace Espo\Core\Select;

use Espo\Core\Select\Where\Item as WhereItem;
use Espo\Core\Utils\Json;

use InvalidArgumentException;
use stdClass;


class SearchParams
{
    
    private array $rawParams = [];

    public const ORDER_ASC = 'ASC';
    public const ORDER_DESC = 'DESC';

    private function __construct() {}

    
    public function getRaw(): array
    {
        return $this->rawParams;
    }

    
    public function getSelect(): ?array
    {
        return $this->rawParams['select'] ?? null;
    }

    
    public function getOrderBy(): ?string
    {
        return $this->rawParams['orderBy'] ?? null;
    }

    
    public function getOrder(): ?string
    {
        return $this->rawParams['order'] ?? null;
    }

    
    public function getOffset(): ?int
    {
        return $this->rawParams['offset'] ?? null;
    }

    
    public function getMaxSize(): ?int
    {
        return $this->rawParams['maxSize'] ?? null;
    }

    
    public function getTextFilter(): ?string
    {
        return $this->rawParams['textFilter'] ?? null;
    }

    
    public function getPrimaryFilter(): ?string
    {
        return $this->rawParams['primaryFilter'] ?? null;
    }

    
    public function getBoolFilterList(): array
    {
        return $this->rawParams['boolFilterList'] ?? [];
    }

    
    public function getWhere(): ?WhereItem
    {
        $raw = $this->rawParams['where'] ?? null;

        if ($raw === null) {
            return null;
        }

        return WhereItem::fromRaw([
            'type' => 'and',
            'value' => $raw,
        ]);
    }

    
    public function getMaxTextAttributeLength(): ?int
    {
        return $this->rawParams['maxTextAttributeLength'] ?? null;
    }

    
    public function withSelect(?array $select): self
    {
        $obj = clone $this;
        $obj->rawParams['select'] = $select;

        return $obj;
    }

    
    public function withOrderBy(?string $orderBy): self
    {
        $obj = clone $this;
        $obj->rawParams['orderBy'] = $orderBy;

        return $obj;
    }

    
    public function withOrder(?string $order): self
    {
        $obj = clone $this;
        $obj->rawParams['order'] = $order;

        if (
            $order !== null &&
            $order !== self::ORDER_ASC &&
            $order !== self::ORDER_DESC
        ) {
            throw new InvalidArgumentException("order value is bad.");
        }

        return $obj;
    }

    
    public function withOffset(?int $offset): self
    {
        $obj = clone $this;
        $obj->rawParams['offset'] = $offset;

        return $obj;
    }

    
    public function withMaxSize(?int $maxSize): self
    {
        $obj = clone $this;
        $obj->rawParams['maxSize'] = $maxSize;

        return $obj;
    }

    
    public function withTextFilter(?string $filter): self
    {
        $obj = clone $this;
        $obj->rawParams['textFilter'] = $filter;

        return $obj;
    }

    
    public function withPrimaryFilter(?string $primaryFilter): self
    {
        $obj = clone $this;
        $obj->rawParams['primaryFilter'] = $primaryFilter;

        return $obj;
    }

    
    public function withBoolFilterList(array $boolFilterList): self
    {
        $obj = clone $this;
        $obj->rawParams['boolFilterList'] = $boolFilterList;

        return $obj;
    }

    public function withBoolFilterAdded(string $boolFilter): self
    {
        $obj = clone $this;
        $obj->rawParams['boolFilterList'] ??= [];
        $obj->rawParams['boolFilterList'][] = $boolFilter;

        return $obj;
    }

    
    public function withWhere(WhereItem $where): self
    {
        $obj = clone $this;

        if ($where->getType() === WhereItem\Type::AND) {
            $obj->rawParams['where'] = $where->getValue() ?? [];

            return $obj;
        }

        $obj->rawParams['where'] = [$where->getRaw()];

        return $obj;
    }

    
    public function withWhereAdded(WhereItem $whereItem): self
    {
        $obj = clone $this;

        $rawWhere = $obj->rawParams['where'] ?? [];

        $rawWhere[] = $whereItem->getRaw();

        $obj->rawParams['where'] = $rawWhere;

        return $obj;
    }

    
    public function withMaxTextAttributeLength(?int $value): self
    {
        $obj = clone $this;

        $obj->rawParams['maxTextAttributeLength'] = $value;

        return $obj;
    }

    
    public static function create(): self
    {
        return new self();
    }

    
    public static function fromRaw($params): self
    {
        if (!is_array($params) && !$params instanceof stdClass) {
            throw new InvalidArgumentException();
        }

        if ($params instanceof stdClass) {
            $params = json_decode(Json::encode($params), true);
        }

        $object = new self();

        $rawParams = [];

        $select = $params['select'] ?? null;
        $orderBy = $params['orderBy'] ?? null;
        $order = $params['order'] ?? null;

        $offset = $params['offset'] ?? null;
        $maxSize = $params['maxSize'] ?? null;

        
        if (is_string($offset) && is_numeric($offset)) {
            $offset = (int) $offset;
        }

        
        if (is_string($maxSize) && is_numeric($maxSize)) {
            $maxSize = (int) $maxSize;
        }

        $boolFilterList = $params['boolFilterList'] ?? [];
        $primaryFilter = $params['primaryFilter'] ?? null;
        $textFilter = $params['textFilter'] ?? $params['q'] ?? null;

        $where = $params['where'] ?? null;

        $maxTextAttributeLength = $params['maxTextAttributeLength'] ?? null;

        if ($select !== null && !is_array($select)) {
            throw new InvalidArgumentException("select should be array.");
        }

        if (is_array($select)) {
            foreach ($select as $item) {
                if (!is_string($item)) {
                    throw new InvalidArgumentException("select has non-string item.");
                }
            }
        }

        if ($orderBy !== null && !is_string($orderBy)) {
            throw new InvalidArgumentException("orderBy should be string.");
        }

        if ($order !== null && !is_string($order)) {
            throw new InvalidArgumentException("order should be string.");
        }

        if (!is_array($boolFilterList)) {
            throw new InvalidArgumentException("boolFilterList should be array.");
        }

        foreach ($boolFilterList as $item) {
            if (!is_string($item)) {
                throw new InvalidArgumentException("boolFilterList has non-string item.");
            }
        }

        if ($primaryFilter !== null && !is_string($primaryFilter)) {
            throw new InvalidArgumentException("primaryFilter should be string.");
        }

        if ($textFilter !== null && !is_string($textFilter)) {
            throw new InvalidArgumentException("textFilter should be string.");
        }

        if ($where !== null && !is_array($where)) {
            throw new InvalidArgumentException("where should be array.");
        }

        if ($offset !== null && !is_int($offset)) {
            throw new InvalidArgumentException("offset should be int.");
        }

        if ($maxSize !== null && !is_int($maxSize)) {
            throw new InvalidArgumentException("maxSize should be int.");
        }

        if ($maxTextAttributeLength && !is_int($maxTextAttributeLength)) {
            throw new InvalidArgumentException("maxTextAttributeLength should be int.");
        }

        if ($order) {
            $order = strtoupper($order);

            if ($order !== self::ORDER_ASC && $order !== self::ORDER_DESC) {
                throw new InvalidArgumentException("order value is bad.");
            }
        }

        $rawParams['select'] = $select;
        $rawParams['orderBy'] = $orderBy;
        $rawParams['order'] = $order;
        $rawParams['offset'] = $offset;
        $rawParams['maxSize'] = $maxSize;
        $rawParams['boolFilterList'] = $boolFilterList;
        $rawParams['primaryFilter'] = $primaryFilter;
        $rawParams['textFilter'] = $textFilter;
        $rawParams['where'] = $where;
        $rawParams['maxTextAttributeLength'] = $maxTextAttributeLength;

        if ($where) {
            $object->adjustParams($rawParams);
        }

        $object->rawParams = $rawParams;

        return $object;
    }

    
    public static function merge(self $searchParams1, self $searchParams2): self
    {
        $paramList = [
            'select',
            'orderBy',
            'order',
            'maxSize',
            'primaryFilter',
            'textFilter',
        ];

        $params = $searchParams2->getRaw();

        $leftParams = $searchParams1->getRaw();

        foreach ($paramList as $name) {
            if (!is_null($leftParams[$name])) {
                $params[$name] = $leftParams[$name];
            }
        }

        foreach ($leftParams['boolFilterList'] as $item) {
            if (in_array($item, $params['boolFilterList'])) {
                continue;
            }

            $params['boolFilterList'][] = $item;
        }

        $params['where'] = $params['where'] ?? [];

        if (!is_null($leftParams['where'])) {
            foreach ($leftParams['where'] as $item) {
                $params['where'][] = $item;
            }
        }

        if (count($params['where']) === 0) {
            $params['where'] = null;
        }

        return self::fromRaw($params);
    }

    
    private function adjustParams(array &$params): void
    {
        if (!$params['where']) {
            return;
        }

        $where = $params['where'];

        foreach ($where as $i => $item) {
            $type = $item['type'] ?? null;
            $value = $item['value'] ?? null;

            if ($type == 'bool' && !empty($value) && is_array($value)) {
                $params['boolFilterList'] = $value;

                unset($where[$i]);
            }
            else if ($type === 'textFilter') {
                $params['textFilter'] = $value;

                unset($where[$i]);
            }
            else if ($type == 'primary' && $value) {
                $params['primaryFilter'] = $value;

                unset($where[$i]);
            }
        }

        $params['where'] = array_values($where);
    }
}

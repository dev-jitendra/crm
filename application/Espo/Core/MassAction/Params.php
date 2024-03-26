<?php


namespace Espo\Core\MassAction;

use Espo\Core\Select\SearchParams;

use RuntimeException;


class Params
{
    private string $entityType;
    
    private ?array $ids = null;
    private ?SearchParams $searchParams = null;

    private function __construct() {}

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    
    public function getIds(): array
    {
        if (!$this->ids) {
            throw new RuntimeException("No IDs.");
        }

        return $this->ids;
    }

    public function getSearchParams(): SearchParams
    {
        if (!$this->searchParams) {
            throw new RuntimeException("No search params.");
        }

        return $this->searchParams;
    }

    public function hasIds(): bool
    {
        return !is_null($this->ids);
    }

    
    public static function createWithIds(string $entityType, array $ids): self
    {
        return self::fromRaw([
            'entityType' => $entityType,
            'ids' => $ids,
        ]);
    }

    public static function createWithSearchParams(string $entityType, SearchParams $searchParams): self
    {
        $obj = new self();

        $obj->entityType = $entityType;
        $obj->searchParams = $searchParams;

        return $obj;
    }

    
    public static function fromRaw(array $params, ?string $entityType = null): self
    {
        

        $obj = new self();

        $passedEntityType = $entityType ?? $params['entityType'] ?? null;

        if (!$passedEntityType) {
            throw new RuntimeException("No 'entityType'.");
        }

        $obj->entityType = $passedEntityType;

        $where = $params['where'] ?? null;
        $ids = $params['ids'] ?? null;

        $searchParams = $params['searchParams'] ?? $params['selectData'] ?? null;

        if ($where !== null && !is_array($where)) {
            throw new RuntimeException("Bad 'where'.");
        }

        if ($searchParams !== null && !is_array($searchParams)) {
            throw new RuntimeException("Bad 'searchParams'.");
        }

        if ($where !== null && $searchParams !== null) {
            $searchParams['where'] = $where;
        }

        if ($where !== null && $searchParams === null) {
            $searchParams = [
                'where' => $where,
            ];
        }

        if ($searchParams !== null) {
            if ($ids !== null) {
                throw new RuntimeException("Can't combine 'ids' and search params.");
            }
        }
        else if ($ids !== null) {
            if (!is_array($ids)) {
                throw new RuntimeException("Bad 'ids'.");
            }

            $obj->ids = $ids;
        }
        else {
            throw new RuntimeException("Bad mass action params.");
        }

        if ($searchParams !== null) {
            $actualSearchParams = $searchParams;

            unset($actualSearchParams['select']);

            $obj->searchParams = SearchParams::fromRaw($actualSearchParams);
        }

        return $obj;
    }

    
    public function __clone()
    {
        if ($this->searchParams) {
            $this->searchParams = clone $this->searchParams;
        }
    }

    
    public function __serialize(): array
    {
        return [
            'entityType' => $this->entityType,
            'ids' => $this->ids,
            'searchParams' => serialize($this->searchParams),
        ];
    }

    
    public function __unserialize(array $data): void
    {
        $this->entityType = $data['entityType'];
        $this->ids = $data['ids'];
        $this->searchParams = unserialize($data['searchParams']);
    }
}

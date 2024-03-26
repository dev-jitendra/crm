<?php


namespace Espo\Tools\Export;

use Espo\Core\Select\SearchParams;
use Espo\Core\Select\Where\Item as WhereItem;

use RuntimeException;


class Params
{
    private string $entityType;
    
    private $attributeList = null;
    
    private $fieldList = null;
    private ?string $fileName = null;
    private ?string $format = null;
    private ?string $name = null;
    
    private array $params = [];
    private ?SearchParams $searchParams = null;
    private bool $applyAccessControl = true;

    public function __construct(string $entityType)
    {
        $this->entityType = $entityType;
    }

    
    public static function fromRaw(array $params): self
    {
        $entityType = $params['entityType'] ?? null;

        if (!$entityType) {
            throw new RuntimeException("No entityType.");
        }

        $obj = new self($entityType);

        $obj->name = $params['name'] ?? $params['exportName'] ?? null;

        $obj->fileName = $params['fileName'] ?? null;
        $obj->format = $params['format'] ?? null;
        $obj->attributeList = $params['attributeList'] ?? null;
        $obj->fieldList = $params['fieldList'] ?? null;

        $where = $params['where'] ?? null;
        $ids = $params['ids'] ?? null;

        $searchParams = $params['searchParams'] ?? null;

        if ($where && !is_array($where)) {
            throw new RuntimeException("Bad 'where'.");
        }

        if ($searchParams && !is_array($searchParams)) {
            throw new RuntimeException("Bad 'searchParams'.");
        }

        if ($where && $searchParams) {
            $searchParams['where'] = $where;
        }

        if ($where && !$searchParams) {
            $searchParams = [
                'where' => $where,
            ];
        }

        if ($searchParams) {
            if ($ids) {
                throw new RuntimeException("Can't combine 'ids' and search params.");
            }
        }
        else if ($ids) {
            if (!is_array($ids)) {
                throw new RuntimeException("Bad 'ids'.");
            }

            $obj->searchParams = SearchParams
                ::create()
                ->withWhere(
                    WhereItem::fromRaw([
                        'type' => 'equals',
                        'attribute' => 'id',
                        'value' => $ids,
                    ])
                );
        }

        if ($searchParams) {
            $actualSearchParams = $searchParams;

            unset($actualSearchParams['select']);

            $obj->searchParams = SearchParams::fromRaw($actualSearchParams);
        }

        return $obj;
    }

    public static function create(string $entityType): self
    {
        return new self($entityType);
    }

    public function withFormat(?string $format): self
    {
        $obj = clone $this;
        $obj->format = $format;

        return $obj;
    }

    
    public function withFileName(?string $fileName): self
    {
        $obj = clone $this;
        $obj->fileName = $fileName;

        return $obj;
    }

    public function withName(?string $name): self
    {
        $obj = clone $this;
        $obj->name = $name;

        return $obj;
    }

    public function withSearchParams(?SearchParams $searchParams): self
    {
        $obj = clone $this;
        $obj->searchParams = $searchParams;

        return $obj;
    }

    public function withParam(string $name, mixed $value): self
    {
        $obj = clone $this;
        $obj->params[$name] = $value;

        return $obj;
    }

    
    public function withFieldList(?array $fieldList): self
    {
        $obj = clone $this;
        $obj->fieldList = $fieldList;

        return $obj;
    }

    
    public function withAttributeList(?array $attributeList): self
    {
        $obj = clone $this;
        $obj->attributeList = $attributeList;

        return $obj;
    }

    public function withAccessControl(bool $applyAccessControl = true): self
    {
        $obj = clone $this;
        $obj->applyAccessControl = $applyAccessControl;

        return $obj;
    }

    
    public function getSearchParams(): SearchParams
    {
        $searchParams = $this->searchParams ?? SearchParams::create();

        if ($searchParams->getSelect() !== null) {
            return $searchParams;
        }

        if ($this->getAttributeList()) {
            $searchParams = $searchParams->withSelect($this->getAttributeList());
        }

        return $searchParams;
    }

    
    public function getEntityType(): string
    {
        return $this->entityType;
    }

    
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    
    public function getName(): ?string
    {
        return $this->name;
    }

    
    public function getFormat(): ?string
    {
        return $this->format;
    }

    
    public function getAttributeList(): ?array
    {
        return $this->attributeList;
    }

    
    public function getFieldList(): ?array
    {
        return $this->fieldList;
    }

    
    public function getParamList(): array
    {
        return array_keys($this->params);
    }

    
    public function getParam(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }

    
    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->params);
    }

    
    public function allFields(): bool
    {
        return $this->fieldList === null && $this->attributeList === null;
    }

    
    public function applyAccessControl(): bool
    {
        return $this->applyAccessControl;
    }
}

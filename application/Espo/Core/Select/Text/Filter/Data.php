<?php


namespace Espo\Core\Select\Text\Filter;

use Espo\ORM\Query\Part\WhereItem;


class Data
{
    private string $filter;
    
    private array $attributeList;
    private bool $skipWildcards = false;
    private ?WhereItem $fullTextSearchWhereItem = null;
    private bool $forceFullTextSearch = false;

    
    public function __construct(string $filter, array $attributeList)
    {
        $this->filter = $filter;
        $this->attributeList = $attributeList;
    }

    
    public static function create(string $filter, array $attributeList): self
    {
        return new self($filter, $attributeList);
    }

    public function withFilter(string $filter): self
    {
        $obj = clone $this;
        $obj->filter = $filter;

        return $obj;
    }

    
    public function withAttributeList(array $attributeList): self
    {
        $obj = clone $this;
        $obj->attributeList = $attributeList;

        return $obj;
    }

    public function withSkipWildcards(bool $skipWildcards = true): self
    {
        $obj = clone $this;
        $obj->skipWildcards = $skipWildcards;

        return $obj;
    }

    public function withForceFullTextSearch(bool $forceFullTextSearch = true): self
    {
        $obj = clone $this;
        $obj->forceFullTextSearch = $forceFullTextSearch;

        return $obj;
    }

    public function withFullTextSearchWhereItem(?WhereItem $fullTextSearchWhereItem): self
    {
        $obj = clone $this;
        $obj->fullTextSearchWhereItem = $fullTextSearchWhereItem;

        return $obj;
    }

    public function getFilter(): string
    {
        return $this->filter;
    }

    
    public function getAttributeList(): array
    {
        return $this->attributeList;
    }

    public function skipWildcards(): bool
    {
        return $this->skipWildcards;
    }

    public function forceFullTextSearch(): bool
    {
        return $this->forceFullTextSearch;
    }

    public function getFullTextSearchWhereItem(): ?WhereItem
    {
        return $this->fullTextSearchWhereItem;
    }
}

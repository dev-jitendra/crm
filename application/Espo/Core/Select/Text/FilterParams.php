<?php


namespace Espo\Core\Select\Text;


class FilterParams
{
    private bool $noFullTextSearch = false;

    private function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function withNoFullTextSearch(bool $noFullTextSearch = true): self
    {
        $obj = clone $this;
        $obj->noFullTextSearch = $noFullTextSearch;

        return $obj;
    }

    public function noFullTextSearch(): bool
    {
        return $this->noFullTextSearch;
    }
}

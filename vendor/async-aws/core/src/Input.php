<?php

namespace AsyncAws\Core;


abstract class Input
{
    
    public $region;

    
    protected function __construct(array $input)
    {
        $this->region = $input['@region'] ?? null;
    }

    public function setRegion(?string $region): void
    {
        $this->region = $region;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    abstract public function request(): Request;
}

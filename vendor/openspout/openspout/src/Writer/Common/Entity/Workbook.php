<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Entity;


final class Workbook
{
    
    private array $worksheets = [];

    
    private readonly string $internalId;

    
    public function __construct()
    {
        $this->internalId = uniqid();
    }

    
    public function getWorksheets(): array
    {
        return $this->worksheets;
    }

    
    public function setWorksheets(array $worksheets): void
    {
        $this->worksheets = $worksheets;
    }

    public function getInternalId(): string
    {
        return $this->internalId;
    }
}

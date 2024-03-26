<?php


namespace Espo\Modules\Crm\Tools\Lead\Convert;

class Params
{
    private bool $skipDuplicateCheck;

    public function __construct(
        bool $skipDuplicateCheck
    ) {
        $this->skipDuplicateCheck = $skipDuplicateCheck;
    }

    public function skipDuplicateCheck(): bool
    {
        return $this->skipDuplicateCheck;
    }
}

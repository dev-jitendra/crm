<?php


namespace Espo\Core\Formula\Functions\UtilGroup;

use Espo\Core\Formula\EvaluatedArgumentList;
use Espo\Core\Formula\Func;
use Espo\Core\Utils\Id\RecordIdGenerator;

class GenerateRecordIdType implements Func
{
    public function __construct(private RecordIdGenerator $recordIdGenerator) {}

    public function process(EvaluatedArgumentList $arguments): string
    {
        return $this->recordIdGenerator->generate();
    }
}

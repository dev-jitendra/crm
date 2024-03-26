<?php


namespace Espo\Core\Utils\Id;

interface RecordIdGenerator
{
    public function generate(): string;
}

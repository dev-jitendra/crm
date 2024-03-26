<?php


namespace Espo\Core\Job;

use Espo\Core\Job\Preparator\Data;

use DateTimeImmutable;


interface Preparator
{
    
    public function prepare(Data $data, DateTimeImmutable $executeTime): void;
}

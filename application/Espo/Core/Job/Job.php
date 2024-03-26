<?php


namespace Espo\Core\Job;

use Espo\Core\Job\Job\Data;


interface Job
{
    
    public function run(Data $data): void;
}

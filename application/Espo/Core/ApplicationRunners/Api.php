<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\Api\Starter;


class Api implements Runner
{
    public function __construct(private Starter $starter)
    {}

    public function run(): void
    {
        $this->starter->start();
    }
}

<?php


namespace Espo\Core\Portal\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\Portal\Api\Starter;

class Api implements Runner
{
    private $starter;

    public function __construct(Starter $starter)
    {
        $this->starter = $starter;
    }

    public function run(): void
    {
        $this->starter->start();
    }
}

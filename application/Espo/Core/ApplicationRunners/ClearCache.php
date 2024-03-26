<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\DataManager;
use Espo\Core\Exceptions\Error;


class ClearCache implements Runner
{
    use Cli;

    public function __construct(private DataManager $dataManager)
    {}

    
    public function run(): void
    {
        $this->dataManager->clearCache();
    }
}

<?php


namespace Espo\Tools\App\Jobs;

use Espo\Core\DataManager;
use Espo\Core\Exceptions\Error;
use Espo\Core\Job\JobDataLess;

class ClearCache implements JobDataLess
{
    private DataManager $dataManager;

    public function __construct(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
    }

    
    public function run(): void
    {
        $this->dataManager->clearCache();
    }
}

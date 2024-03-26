<?php


namespace Espo\Classes\Jobs;

use Espo\Core\Exceptions\Error;

use Espo\Core\Mail\Account\PersonalAccount\Service;
use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;

use Throwable;

class CheckEmailAccounts implements Job
{
    private $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function run(Data $data): void
    {
        $targetId = $data->getTargetId();

        if (!$targetId) {
            throw new Error("No target.");
        }

        try {
            $this->service->fetch($targetId);
        }
        catch (Throwable $e) {
            throw new Error(
                'Job CheckEmailAccounts ' . $targetId . ': [' . $e->getCode() . '] ' . $e->getMessage() . ' ' .
                $e->getFile() . ':' . $e->getLine()
            );
        }
    }
}

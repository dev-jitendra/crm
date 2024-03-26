<?php


namespace Espo\Classes\Jobs;

use Espo\Core\Exceptions\Error;
use Espo\Core\Mail\Account\GroupAccount\Service;
use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;

use Throwable;

class CheckInboundEmails implements Job
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
                'Job CheckInboundEmails ' . $targetId . ': [' . $e->getCode() . '] ' .$e->getMessage()
            );
        }
    }
}

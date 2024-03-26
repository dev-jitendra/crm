<?php


namespace Espo\Tools\LeadCapture\Jobs;

use Espo\Core\Exceptions\Error;
use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;
use Espo\Tools\LeadCapture\ConfirmationSender;
use RuntimeException;

class OptInConfirmation implements Job
{
    private ConfirmationSender $sender;

    public function __construct(ConfirmationSender $sender)
    {
        $this->sender = $sender;
    }

    
    public function run(Data $data): void
    {
        $uniqueId = $data->get('id');

        if (!$uniqueId) {
            throw new RuntimeException();
        }

        $this->sender->send($uniqueId);
    }
}

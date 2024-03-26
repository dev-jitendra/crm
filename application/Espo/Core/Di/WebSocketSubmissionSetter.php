<?php


namespace Espo\Core\Di;

use Espo\Core\WebSocket\Submission;

trait WebSocketSubmissionSetter
{
    
    protected $webSocketSubmission;

    public function setWebSocketSubmission(Submission $webSocketSubmission): void
    {
        $this->webSocketSubmission = $webSocketSubmission;
    }
}

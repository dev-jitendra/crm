<?php


namespace Espo\Core\Di;

use Espo\Core\WebSocket\Submission;

interface WebSocketSubmissionAware
{
    public function setWebSocketSubmission(Submission $webSocketSubmission): void;
}

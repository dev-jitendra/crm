<?php


namespace Espo\Core\WebSocket;

use Espo\Core\Utils\Log;
use Espo\Core\Utils\Json;

use stdClass;
use Throwable;

class Submission
{
    public function __construct(
        private Sender $sender,
        private Log $log
    ) {}

    
    public function submit(string $topic, ?string $userId = null, ?stdClass $data = null): void
    {
        if (!$data) {
            $data = (object) [];
        }

        if ($userId) {
            $data->userId = $userId;
        }

        $data->topicId = $topic;

        $message = Json::encode($data);

        try {
            $this->sender->send($message);
        }
        catch (Throwable $e) {
            $this->log->error("WebSocketSubmission: " . $e->getMessage());
        }
    }
}

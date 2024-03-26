<?php


namespace Espo\Core\Mail\Account\GroupAccount;

use Espo\Core\Mail\Message;
use Espo\Core\Mail\Message\Part;

class BouncedRecognizer
{
    
    private array $hardBounceCodeList = [
        '5.0.0',
        '5.1.1', 
        '5.1.2', 
        '5.1.6', 
        '5.4.1', 
    ];

    public function isBounced(Message $message): bool
    {
        $from = $message->getHeader('From');
        $contentType = $message->getHeader('Content-Type');

        if (preg_match('/MAILER-DAEMON|POSTMASTER/i', $from ?? '')) {
            return true;
        }

        if (strpos($contentType ?? '', 'multipart/report') === 0) {
            
            $deliveryStatusPart = $this->getDeliveryStatusPart($message);

            if ($deliveryStatusPart) {
                return true;
            }

            $content = $message->getRawContent();

            if (
                strpos($content, 'message/delivery-status') !== false &&
                strpos($content, 'Status: ') !== false
            ) {
                return true;
            }
        }

        return false;
    }

    public function isHard(Message $message): bool
    {
        $content = $message->getRawContent();

        if (preg_match('/permanent[ ]*[error|failure]/', $content)) {
            return true;
        }

        $m = null;

        $has5xxStatus = preg_match('/Status: (5\.[0-9]\.[0-9])/', $content, $m);

        if ($has5xxStatus) {
            $status = $m[1] ?? null;

            if (in_array($status, $this->hardBounceCodeList)) {
                return true;
            }
        }

        return false;
    }

    public function extractStatus(Message $message): ?string
    {
        $content = $message->getRawContent();

        $m = null;

        $hasStatus = preg_match('/Status: ([0-9]\.[0-9]\.[0-9])/', $content, $m);

        if ($hasStatus) {
            $status = $m[1] ?? null;

            return $status;
        }

        return null;
    }

    public function extractQueueItemId(Message $message): ?string
    {
        $content = $message->getRawContent();

        if (preg_match('/X-Queue-Item-Id: [a-z0-9\-]*/', $content, $m)) {
            
            $arr = preg_split('/X-Queue-Item-Id: /', $m[0], -1, \PREG_SPLIT_NO_EMPTY);

            return $arr[0];
        }

        $to = $message->getHeader('to');

        if (preg_match('/\+bounce-qid-[a-z0-9\-]*/', $to ?? '', $m)) {
            
            $arr = preg_split('/\+bounce-qid-/', $m[0], -1, \PREG_SPLIT_NO_EMPTY);

            return $arr[0];
        }

        return null;
    }

    private function getDeliveryStatusPart(Message $message): ?Part
    {
        foreach ($message->getPartList() as $part) {
            if ($part->getContentType() === 'message/delivery-status') {
                return $part;
            }
        }

        return null;
    }
}

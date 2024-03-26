<?php
namespace Ratchet\Http;
use Ratchet\MessageInterface;
use Ratchet\ConnectionInterface;
use GuzzleHttp\Psr7\Message;


class HttpRequestParser implements MessageInterface {
    const EOM = "\r\n\r\n";

    
    public $maxSize = 4096;

    
    public function onMessage(ConnectionInterface $context, $data) {
        if (!isset($context->httpBuffer)) {
            $context->httpBuffer = '';
        }

        $context->httpBuffer .= $data;

        if (strlen($context->httpBuffer) > (int)$this->maxSize) {
            throw new \OverflowException("Maximum buffer size of {$this->maxSize} exceeded parsing HTTP header");
        }

        if ($this->isEom($context->httpBuffer)) {
            $request = $this->parse($context->httpBuffer);

            unset($context->httpBuffer);

            return $request;
        }
    }

    
    public function isEom($message) {
        return (boolean)strpos($message, static::EOM);
    }

    
    public function parse($headers) {
        return Message::parseRequest($headers);
    }
}

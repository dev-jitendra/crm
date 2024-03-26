<?php



namespace Symfony\Component\HttpClient\Response;

use Symfony\Component\HttpClient\Chunk\ErrorChunk;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\TraceableHttpClient;
use Symfony\Component\Stopwatch\StopwatchEvent;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


class TraceableResponse implements ResponseInterface, StreamableInterface
{
    private $client;
    private $response;
    private $content;
    private $event;

    public function __construct(HttpClientInterface $client, ResponseInterface $response, &$content, StopwatchEvent $event = null)
    {
        $this->client = $client;
        $this->response = $response;
        $this->content = &$content;
        $this->event = $event;
    }

    
    public function __sleep()
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    public function __wakeup()
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }

    public function __destruct()
    {
        try {
            $this->response->__destruct();
        } finally {
            if ($this->event && $this->event->isStarted()) {
                $this->event->stop();
            }
        }
    }

    public function getStatusCode(): int
    {
        try {
            return $this->response->getStatusCode();
        } finally {
            if ($this->event && $this->event->isStarted()) {
                $this->event->lap();
            }
        }
    }

    public function getHeaders(bool $throw = true): array
    {
        try {
            return $this->response->getHeaders($throw);
        } finally {
            if ($this->event && $this->event->isStarted()) {
                $this->event->lap();
            }
        }
    }

    public function getContent(bool $throw = true): string
    {
        try {
            if (false === $this->content) {
                return $this->response->getContent($throw);
            }

            return $this->content = $this->response->getContent(false);
        } finally {
            if ($this->event && $this->event->isStarted()) {
                $this->event->stop();
            }
            if ($throw) {
                $this->checkStatusCode($this->response->getStatusCode());
            }
        }
    }

    public function toArray(bool $throw = true): array
    {
        try {
            if (false === $this->content) {
                return $this->response->toArray($throw);
            }

            return $this->content = $this->response->toArray(false);
        } finally {
            if ($this->event && $this->event->isStarted()) {
                $this->event->stop();
            }
            if ($throw) {
                $this->checkStatusCode($this->response->getStatusCode());
            }
        }
    }

    public function cancel(): void
    {
        $this->response->cancel();

        if ($this->event && $this->event->isStarted()) {
            $this->event->stop();
        }
    }

    public function getInfo(string $type = null)
    {
        return $this->response->getInfo($type);
    }

    
    public function toStream(bool $throw = true)
    {
        if ($throw) {
            
            $this->response->getHeaders(true);
        }

        if ($this->response instanceof StreamableInterface) {
            return $this->response->toStream(false);
        }

        return StreamWrapper::createResource($this->response, $this->client);
    }

    
    public static function stream(HttpClientInterface $client, iterable $responses, ?float $timeout): \Generator
    {
        $wrappedResponses = [];
        $traceableMap = new \SplObjectStorage();

        foreach ($responses as $r) {
            if (!$r instanceof self) {
                throw new \TypeError(sprintf('"%s::stream()" expects parameter 1 to be an iterable of TraceableResponse objects, "%s" given.', TraceableHttpClient::class, get_debug_type($r)));
            }

            $traceableMap[$r->response] = $r;
            $wrappedResponses[] = $r->response;
            if ($r->event && !$r->event->isStarted()) {
                $r->event->start();
            }
        }

        foreach ($client->stream($wrappedResponses, $timeout) as $r => $chunk) {
            if ($traceableMap[$r]->event && $traceableMap[$r]->event->isStarted()) {
                try {
                    if ($chunk->isTimeout() || !$chunk->isLast()) {
                        $traceableMap[$r]->event->lap();
                    } else {
                        $traceableMap[$r]->event->stop();
                    }
                } catch (TransportExceptionInterface $e) {
                    $traceableMap[$r]->event->stop();
                    if ($chunk instanceof ErrorChunk) {
                        $chunk->didThrow(false);
                    } else {
                        $chunk = new ErrorChunk($chunk->getOffset(), $e);
                    }
                }
            }
            yield $traceableMap[$r] => $chunk;
        }
    }

    private function checkStatusCode(int $code)
    {
        if (500 <= $code) {
            throw new ServerException($this);
        }

        if (400 <= $code) {
            throw new ClientException($this);
        }

        if (300 <= $code) {
            throw new RedirectionException($this);
        }
    }
}

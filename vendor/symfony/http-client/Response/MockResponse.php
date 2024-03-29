<?php



namespace Symfony\Component\HttpClient\Response;

use Symfony\Component\HttpClient\Chunk\ErrorChunk;
use Symfony\Component\HttpClient\Chunk\FirstChunk;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\Internal\ClientState;
use Symfony\Contracts\HttpClient\ResponseInterface;


class MockResponse implements ResponseInterface, StreamableInterface
{
    use CommonResponseTrait;
    use TransportResponseTrait {
        doDestruct as public __destruct;
    }

    private $body;
    private $requestOptions = [];
    private $requestUrl;
    private $requestMethod;

    private static $mainMulti;
    private static $idSequence = 0;

    
    public function __construct($body = '', array $info = [])
    {
        $this->body = is_iterable($body) ? $body : (string) $body;
        $this->info = $info + ['http_code' => 200] + $this->info;

        if (!isset($info['response_headers'])) {
            return;
        }

        $responseHeaders = [];

        foreach ($info['response_headers'] as $k => $v) {
            foreach ((array) $v as $v) {
                $responseHeaders[] = (\is_string($k) ? $k.': ' : '').$v;
            }
        }

        $this->info['response_headers'] = [];
        self::addResponseHeaders($responseHeaders, $this->info, $this->headers);
    }

    
    public function getRequestOptions(): array
    {
        return $this->requestOptions;
    }

    
    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    
    public function getInfo(string $type = null)
    {
        return null !== $type ? $this->info[$type] ?? null : $this->info;
    }

    
    public function cancel(): void
    {
        $this->info['canceled'] = true;
        $this->info['error'] = 'Response has been canceled.';
        $this->body = null;
    }

    
    protected function close(): void
    {
        $this->inflate = null;
        $this->body = [];
    }

    
    public static function fromRequest(string $method, string $url, array $options, ResponseInterface $mock): self
    {
        $response = new self([]);
        $response->requestOptions = $options;
        $response->id = ++self::$idSequence;
        $response->shouldBuffer = $options['buffer'] ?? true;
        $response->initializer = static function (self $response) {
            return \is_array($response->body[0] ?? null);
        };

        $response->info['redirect_count'] = 0;
        $response->info['redirect_url'] = null;
        $response->info['start_time'] = microtime(true);
        $response->info['http_method'] = $method;
        $response->info['http_code'] = 0;
        $response->info['user_data'] = $options['user_data'] ?? null;
        $response->info['url'] = $url;

        if ($mock instanceof self) {
            $mock->requestOptions = $response->requestOptions;
            $mock->requestMethod = $method;
            $mock->requestUrl = $url;
        }

        self::writeRequest($response, $options, $mock);
        $response->body[] = [$options, $mock];

        return $response;
    }

    
    protected static function schedule(self $response, array &$runningResponses): void
    {
        if (!$response->id) {
            throw new InvalidArgumentException('MockResponse instances must be issued by MockHttpClient before processing.');
        }

        $multi = self::$mainMulti ?? self::$mainMulti = new ClientState();

        if (!isset($runningResponses[0])) {
            $runningResponses[0] = [$multi, []];
        }

        $runningResponses[0][1][$response->id] = $response;
    }

    
    protected static function perform(ClientState $multi, array &$responses): void
    {
        foreach ($responses as $response) {
            $id = $response->id;

            if (null === $response->body) {
                
                $response->body = [];
            } elseif ([] === $response->body) {
                
                $multi->handlesActivity[$id][] = null;
                $multi->handlesActivity[$id][] = null !== $response->info['error'] ? new TransportException($response->info['error']) : null;
            } elseif (null === $chunk = array_shift($response->body)) {
                
                $multi->handlesActivity[$id][] = null;
                $multi->handlesActivity[$id][] = array_shift($response->body);
            } elseif (\is_array($chunk)) {
                
                try {
                    $offset = 0;
                    $chunk[1]->getStatusCode();
                    $chunk[1]->getHeaders(false);
                    self::readResponse($response, $chunk[0], $chunk[1], $offset);
                    $multi->handlesActivity[$id][] = new FirstChunk();
                    $buffer = $response->requestOptions['buffer'] ?? null;

                    if ($buffer instanceof \Closure && $response->content = $buffer($response->headers) ?: null) {
                        $response->content = \is_resource($response->content) ? $response->content : fopen('php:
                    }
                } catch (\Throwable $e) {
                    $multi->handlesActivity[$id][] = null;
                    $multi->handlesActivity[$id][] = $e;
                }
            } else {
                
                $multi->handlesActivity[$id][] = $chunk;
            }
        }
    }

    
    protected static function select(ClientState $multi, float $timeout): int
    {
        return 42;
    }

    
    private static function writeRequest(self $response, array $options, ResponseInterface $mock)
    {
        $onProgress = $options['on_progress'] ?? static function () {};
        $response->info += $mock->getInfo() ?: [];

        
        if (isset($response->info['size_upload'])) {
            $response->info['size_upload'] = 0.0;
        }

        
        if (!isset($response->info['total_time'])) {
            $response->info['total_time'] = microtime(true) - $response->info['start_time'];
        }

        
        $onProgress(0, 0, $response->info);

        
        if (\is_resource($body = $options['body'] ?? '')) {
            $data = stream_get_contents($body);
            if (isset($response->info['size_upload'])) {
                $response->info['size_upload'] += \strlen($data);
            }
        } elseif ($body instanceof \Closure) {
            while ('' !== $data = $body(16372)) {
                if (!\is_string($data)) {
                    throw new TransportException(sprintf('Return value of the "body" option callback must be string, "%s" returned.', get_debug_type($data)));
                }

                
                if (isset($response->info['size_upload'])) {
                    $response->info['size_upload'] += \strlen($data);
                }

                $onProgress(0, 0, $response->info);
            }
        }
    }

    
    private static function readResponse(self $response, array $options, ResponseInterface $mock, int &$offset)
    {
        $onProgress = $options['on_progress'] ?? static function () {};

        
        $info = $mock->getInfo() ?: [];
        $response->info['http_code'] = ($info['http_code'] ?? 0) ?: $mock->getStatusCode() ?: 200;
        $response->addResponseHeaders($info['response_headers'] ?? [], $response->info, $response->headers);
        $dlSize = isset($response->headers['content-encoding']) || 'HEAD' === $response->info['http_method'] || \in_array($response->info['http_code'], [204, 304], true) ? 0 : (int) ($response->headers['content-length'][0] ?? 0);

        $response->info = [
            'start_time' => $response->info['start_time'],
            'user_data' => $response->info['user_data'],
            'http_code' => $response->info['http_code'],
        ] + $info + $response->info;

        if (!isset($response->info['total_time'])) {
            $response->info['total_time'] = microtime(true) - $response->info['start_time'];
        }

        
        $onProgress(0, $dlSize, $response->info);

        
        $body = $mock instanceof self ? $mock->body : $mock->getContent(false);

        if (!\is_string($body)) {
            foreach ($body as $chunk) {
                if ('' === $chunk = (string) $chunk) {
                    
                    $response->body[] = new ErrorChunk($offset, sprintf('Idle timeout reached for "%s".', $response->info['url']));
                } else {
                    $response->body[] = $chunk;
                    $offset += \strlen($chunk);
                    
                    $onProgress($offset, $dlSize, $response->info);
                }
            }
        } elseif ('' !== $body) {
            $response->body[] = $body;
            $offset = \strlen($body);
        }

        if (!isset($response->info['total_time'])) {
            $response->info['total_time'] = microtime(true) - $response->info['start_time'];
        }

        
        $onProgress($offset, $dlSize, $response->info);

        if ($dlSize && $offset !== $dlSize) {
            throw new TransportException(sprintf('Transfer closed with %d bytes remaining to read.', $dlSize - $offset));
        }
    }
}

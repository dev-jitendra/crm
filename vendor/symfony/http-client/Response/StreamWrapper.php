<?php



namespace Symfony\Component\HttpClient\Response;

use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


class StreamWrapper
{
    
    public $context;

    
    private $client;

    
    private $response;

    
    private $content;

    
    private $handle;

    private $blocking = true;
    private $timeout;
    private $eof = false;
    private $offset = 0;

    
    public static function createResource(ResponseInterface $response, HttpClientInterface $client = null)
    {
        if ($response instanceof StreamableInterface) {
            $stack = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS, 2);

            if ($response !== ($stack[1]['object'] ?? null)) {
                return $response->toStream(false);
            }
        }

        if (null === $client && !method_exists($response, 'stream')) {
            throw new \InvalidArgumentException(sprintf('Providing a client to "%s()" is required when the response doesn\'t have any "stream()" method.', __CLASS__));
        }

        if (false === stream_wrapper_register('symfony', __CLASS__)) {
            throw new \RuntimeException(error_get_last()['message'] ?? 'Registering the "symfony" stream wrapper failed.');
        }

        try {
            $context = [
                'client' => $client ?? $response,
                'response' => $response,
            ];

            return fopen('symfony:
        } finally {
            stream_wrapper_unregister('symfony');
        }
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    
    public function bindHandles(&$handle, &$content): void
    {
        $this->handle = &$handle;
        $this->content = &$content;
    }

    public function stream_open(string $path, string $mode, int $options): bool
    {
        if ('r' !== $mode) {
            if ($options & \STREAM_REPORT_ERRORS) {
                trigger_error(sprintf('Invalid mode "%s": only "r" is supported.', $mode), \E_USER_WARNING);
            }

            return false;
        }

        $context = stream_context_get_options($this->context)['symfony'] ?? null;
        $this->client = $context['client'] ?? null;
        $this->response = $context['response'] ?? null;
        $this->context = null;

        if (null !== $this->client && null !== $this->response) {
            return true;
        }

        if ($options & \STREAM_REPORT_ERRORS) {
            trigger_error('Missing options "client" or "response" in "symfony" stream context.', \E_USER_WARNING);
        }

        return false;
    }

    public function stream_read(int $count)
    {
        if (\is_resource($this->content)) {
            
            foreach ($this->client->stream([$this->response], 0) as $chunk) {
                try {
                    if (!$chunk->isTimeout() && $chunk->isFirst()) {
                        $this->response->getStatusCode(); 
                    }
                } catch (ExceptionInterface $e) {
                    trigger_error($e->getMessage(), \E_USER_WARNING);

                    return false;
                }
            }

            if (0 !== fseek($this->content, $this->offset)) {
                return false;
            }

            if ('' !== $data = fread($this->content, $count)) {
                fseek($this->content, 0, \SEEK_END);
                $this->offset += \strlen($data);

                return $data;
            }
        }

        if (\is_string($this->content)) {
            if (\strlen($this->content) <= $count) {
                $data = $this->content;
                $this->content = null;
            } else {
                $data = substr($this->content, 0, $count);
                $this->content = substr($this->content, $count);
            }
            $this->offset += \strlen($data);

            return $data;
        }

        foreach ($this->client->stream([$this->response], $this->blocking ? $this->timeout : 0) as $chunk) {
            try {
                $this->eof = true;
                $this->eof = !$chunk->isTimeout();
                $this->eof = $chunk->isLast();

                if ($chunk->isFirst()) {
                    $this->response->getStatusCode(); 
                }

                if ('' !== $data = $chunk->getContent()) {
                    if (\strlen($data) > $count) {
                        if (null === $this->content) {
                            $this->content = substr($data, $count);
                        }
                        $data = substr($data, 0, $count);
                    }
                    $this->offset += \strlen($data);

                    return $data;
                }
            } catch (ExceptionInterface $e) {
                trigger_error($e->getMessage(), \E_USER_WARNING);

                return false;
            }
        }

        return '';
    }

    public function stream_set_option(int $option, int $arg1, ?int $arg2): bool
    {
        if (\STREAM_OPTION_BLOCKING === $option) {
            $this->blocking = (bool) $arg1;
        } elseif (\STREAM_OPTION_READ_TIMEOUT === $option) {
            $this->timeout = $arg1 + $arg2 / 1e6;
        } else {
            return false;
        }

        return true;
    }

    public function stream_tell(): int
    {
        return $this->offset;
    }

    public function stream_eof(): bool
    {
        return $this->eof && !\is_string($this->content);
    }

    public function stream_seek(int $offset, int $whence = \SEEK_SET): bool
    {
        if (!\is_resource($this->content) || 0 !== fseek($this->content, 0, \SEEK_END)) {
            return false;
        }

        $size = ftell($this->content);

        if (\SEEK_CUR === $whence) {
            $offset += $this->offset;
        }

        if (\SEEK_END === $whence || $size < $offset) {
            foreach ($this->client->stream([$this->response]) as $chunk) {
                try {
                    if ($chunk->isFirst()) {
                        $this->response->getStatusCode(); 
                    }

                    
                    $size += \strlen($chunk->getContent());

                    if (\SEEK_END !== $whence && $offset <= $size) {
                        break;
                    }
                } catch (ExceptionInterface $e) {
                    trigger_error($e->getMessage(), \E_USER_WARNING);

                    return false;
                }
            }

            if (\SEEK_END === $whence) {
                $offset += $size;
            }
        }

        if (0 <= $offset && $offset <= $size) {
            $this->eof = false;
            $this->offset = $offset;

            return true;
        }

        return false;
    }

    public function stream_cast(int $castAs)
    {
        if (\STREAM_CAST_FOR_SELECT === $castAs) {
            $this->response->getHeaders(false);

            return (\is_callable($this->handle) ? ($this->handle)() : $this->handle) ?? false;
        }

        return false;
    }

    public function stream_stat(): array
    {
        try {
            $headers = $this->response->getHeaders(false);
        } catch (ExceptionInterface $e) {
            trigger_error($e->getMessage(), \E_USER_WARNING);
            $headers = [];
        }

        return [
            'dev' => 0,
            'ino' => 0,
            'mode' => 33060,
            'nlink' => 0,
            'uid' => 0,
            'gid' => 0,
            'rdev' => 0,
            'size' => (int) ($headers['content-length'][0] ?? -1),
            'atime' => 0,
            'mtime' => strtotime($headers['last-modified'][0] ?? '') ?: 0,
            'ctime' => 0,
            'blksize' => 0,
            'blocks' => 0,
        ];
    }

    private function __construct()
    {
    }
}

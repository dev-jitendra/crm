<?php declare(strict_types=1);



namespace Monolog\Handler;

use Monolog\Level;
use Monolog\Utils;
use Monolog\LogRecord;


class StreamHandler extends AbstractProcessingHandler
{
    protected const MAX_CHUNK_SIZE = 2147483647;
    
    protected const DEFAULT_CHUNK_SIZE = 10 * 1024 * 1024;
    protected int $streamChunkSize;
    
    protected $stream;
    protected string|null $url = null;
    private string|null $errorMessage = null;
    protected int|null $filePermission;
    protected bool $useLocking;
    
    private bool|null $dirCreated = null;

    
    public function __construct($stream, int|string|Level $level = Level::Debug, bool $bubble = true, ?int $filePermission = null, bool $useLocking = false)
    {
        parent::__construct($level, $bubble);

        if (($phpMemoryLimit = Utils::expandIniShorthandBytes(ini_get('memory_limit'))) !== false) {
            if ($phpMemoryLimit > 0) {
                
                $this->streamChunkSize = min(static::MAX_CHUNK_SIZE, max((int) ($phpMemoryLimit / 10), 100 * 1024));
            } else {
                
                $this->streamChunkSize = static::DEFAULT_CHUNK_SIZE;
            }
        } else {
            
            $this->streamChunkSize = static::DEFAULT_CHUNK_SIZE;
        }

        if (is_resource($stream)) {
            $this->stream = $stream;

            stream_set_chunk_size($this->stream, $this->streamChunkSize);
        } elseif (is_string($stream)) {
            $this->url = Utils::canonicalizePath($stream);
        } else {
            throw new \InvalidArgumentException('A stream must either be a resource or a string.');
        }

        $this->filePermission = $filePermission;
        $this->useLocking = $useLocking;
    }

    
    public function close(): void
    {
        if (null !== $this->url && is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->stream = null;
        $this->dirCreated = null;
    }

    
    public function getStream()
    {
        return $this->stream;
    }

    
    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getStreamChunkSize(): int
    {
        return $this->streamChunkSize;
    }

    
    protected function write(LogRecord $record): void
    {
        if (!is_resource($this->stream)) {
            $url = $this->url;
            if (null === $url || '' === $url) {
                throw new \LogicException('Missing stream url, the stream can not be opened. This may be caused by a premature call to close().' . Utils::getRecordMessageForException($record));
            }
            $this->createDir($url);
            $this->errorMessage = null;
            set_error_handler([$this, 'customErrorHandler']);
            try {
                $stream = fopen($url, 'a');
                if ($this->filePermission !== null) {
                    @chmod($url, $this->filePermission);
                }
            } finally {
                restore_error_handler();
            }
            if (!is_resource($stream)) {
                $this->stream = null;

                throw new \UnexpectedValueException(sprintf('The stream or file "%s" could not be opened in append mode: '.$this->errorMessage, $url) . Utils::getRecordMessageForException($record));
            }
            stream_set_chunk_size($stream, $this->streamChunkSize);
            $this->stream = $stream;
        }

        $stream = $this->stream;
        if ($this->useLocking) {
            
            flock($stream, LOCK_EX);
        }

        $this->streamWrite($stream, $record);

        if ($this->useLocking) {
            flock($stream, LOCK_UN);
        }
    }

    
    protected function streamWrite($stream, LogRecord $record): void
    {
        fwrite($stream, (string) $record->formatted);
    }

    private function customErrorHandler(int $code, string $msg): bool
    {
        $this->errorMessage = preg_replace('{^(fopen|mkdir)\(.*?\): }', '', $msg);

        return true;
    }

    private function getDirFromStream(string $stream): ?string
    {
        $pos = strpos($stream, ':
        if ($pos === false) {
            return dirname($stream);
        }

        if ('file:
            return dirname(substr($stream, 7));
        }

        return null;
    }

    private function createDir(string $url): void
    {
        
        if (true === $this->dirCreated) {
            return;
        }

        $dir = $this->getDirFromStream($url);
        if (null !== $dir && !is_dir($dir)) {
            $this->errorMessage = null;
            set_error_handler([$this, 'customErrorHandler']);
            $status = mkdir($dir, 0777, true);
            restore_error_handler();
            if (false === $status && !is_dir($dir) && strpos((string) $this->errorMessage, 'File exists') === false) {
                throw new \UnexpectedValueException(sprintf('There is no existing directory at "%s" and it could not be created: '.$this->errorMessage, $dir));
            }
        }
        $this->dirCreated = true;
    }
}

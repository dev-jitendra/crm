<?php

declare(strict_types=1);

namespace OpenSpout\Reader;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Exception\ReaderException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;


abstract class AbstractReader implements ReaderInterface
{
    
    private bool $isStreamOpened = false;

    
    public function open(string $filePath): void
    {
        if ($this->isStreamWrapper($filePath) && (!$this->doesSupportStreamWrapper() || !$this->isSupportedStreamWrapper($filePath))) {
            throw new IOException("Could not open {$filePath} for reading! Stream wrapper used is not supported for this type of file.");
        }

        if (!$this->isPhpStream($filePath)) {
            
            if (!file_exists($filePath)) {
                throw new IOException("Could not open {$filePath} for reading! File does not exist.");
            }
            if (!is_readable($filePath)) {
                throw new IOException("Could not open {$filePath} for reading! File is not readable.");
            }
        }

        try {
            $fileRealPath = $this->getFileRealPath($filePath);
            $this->openReader($fileRealPath);
            $this->isStreamOpened = true;
        } catch (ReaderException $exception) {
            throw new IOException(
                "Could not open {$filePath} for reading!",
                0,
                $exception
            );
        }
    }

    
    final public function close(): void
    {
        if ($this->isStreamOpened) {
            $this->closeReader();

            $this->isStreamOpened = false;
        }
    }

    
    abstract protected function doesSupportStreamWrapper(): bool;

    
    abstract protected function openReader(string $filePath): void;

    
    abstract protected function closeReader(): void;

    final protected function ensureStreamOpened(): void
    {
        if (!$this->isStreamOpened) {
            throw new ReaderNotOpenedException('Reader should be opened first.');
        }
    }

    
    private function getFileRealPath(string $filePath): string
    {
        if ($this->isSupportedStreamWrapper($filePath)) {
            return $filePath;
        }

        
        $realpath = realpath($filePath);
        \assert(false !== $realpath);

        return $realpath;
    }

    
    private function getStreamWrapperScheme(string $filePath): ?string
    {
        $streamScheme = null;
        if (1 === preg_match('/^(\w+):\/\
            $streamScheme = $matches[1];
        }

        return $streamScheme;
    }

    
    private function isStreamWrapper(string $filePath): bool
    {
        return null !== $this->getStreamWrapperScheme($filePath);
    }

    
    private function isSupportedStreamWrapper(string $filePath): bool
    {
        $streamScheme = $this->getStreamWrapperScheme($filePath);

        return null === $streamScheme || \in_array($streamScheme, stream_get_wrappers(), true);
    }

    
    private function isPhpStream(string $filePath): bool
    {
        $streamScheme = $this->getStreamWrapperScheme($filePath);

        return 'php' === $streamScheme;
    }
}

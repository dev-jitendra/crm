<?php



declare(strict_types=1);

namespace Slim\Psr7;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

use function flush;
use function ob_get_clean;
use function ob_get_level;
use function strlen;

use const SEEK_SET;

class NonBufferedBody implements StreamInterface
{
    
    public function __toString(): string
    {
        return '';
    }

    
    public function close(): void
    {
        throw new RuntimeException('A NonBufferedBody is not closable.');
    }

    
    public function detach()
    {
        return null;
    }

    
    public function getSize(): ?int
    {
        return null;
    }

    
    public function tell(): int
    {
        return 0;
    }

    
    public function eof(): bool
    {
        return true;
    }

    
    public function isSeekable(): bool
    {
        return false;
    }

    
    public function seek($offset, $whence = SEEK_SET): void
    {
        throw new RuntimeException('A NonBufferedBody is not seekable.');
    }

    
    public function rewind(): void
    {
        throw new RuntimeException('A NonBufferedBody is not rewindable.');
    }

    
    public function isWritable(): bool
    {
        return true;
    }

    
    public function write($string): int
    {
        $buffered = '';
        while (0 < ob_get_level()) {
            $buffered = ob_get_clean() . $buffered;
        }

        echo $buffered . $string;

        flush();

        return strlen($string) + strlen($buffered);
    }

    
    public function isReadable(): bool
    {
        return false;
    }

    
    public function read($length): string
    {
        throw new RuntimeException('A NonBufferedBody is not readable.');
    }

    
    public function getContents(): string
    {
        return '';
    }

    
    public function getMetadata($key = null): ?array
    {
        return null;
    }
}

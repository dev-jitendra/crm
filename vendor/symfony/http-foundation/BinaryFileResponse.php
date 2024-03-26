<?php



namespace Symfony\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;


class BinaryFileResponse extends Response
{
    protected static $trustXSendfileTypeHeader = false;

    
    protected $file;
    protected $offset = 0;
    protected $maxlen = -1;
    protected $deleteFileAfterSend = false;
    protected $chunkSize = 8 * 1024;

    
    public function __construct(\SplFileInfo|string $file, int $status = 200, array $headers = [], bool $public = true, string $contentDisposition = null, bool $autoEtag = false, bool $autoLastModified = true)
    {
        parent::__construct(null, $status, $headers);

        $this->setFile($file, $contentDisposition, $autoEtag, $autoLastModified);

        if ($public) {
            $this->setPublic();
        }
    }

    
    public function setFile(\SplFileInfo|string $file, string $contentDisposition = null, bool $autoEtag = false, bool $autoLastModified = true): static
    {
        if (!$file instanceof File) {
            if ($file instanceof \SplFileInfo) {
                $file = new File($file->getPathname());
            } else {
                $file = new File((string) $file);
            }
        }

        if (!$file->isReadable()) {
            throw new FileException('File must be readable.');
        }

        $this->file = $file;

        if ($autoEtag) {
            $this->setAutoEtag();
        }

        if ($autoLastModified) {
            $this->setAutoLastModified();
        }

        if ($contentDisposition) {
            $this->setContentDisposition($contentDisposition);
        }

        return $this;
    }

    
    public function getFile(): File
    {
        return $this->file;
    }

    
    public function setChunkSize(int $chunkSize): static
    {
        if ($chunkSize < 1 || $chunkSize > \PHP_INT_MAX) {
            throw new \LogicException('The chunk size of a BinaryFileResponse cannot be less than 1 or greater than PHP_INT_MAX.');
        }

        $this->chunkSize = $chunkSize;

        return $this;
    }

    
    public function setAutoLastModified(): static
    {
        $this->setLastModified(\DateTime::createFromFormat('U', $this->file->getMTime()));

        return $this;
    }

    
    public function setAutoEtag(): static
    {
        $this->setEtag(base64_encode(hash_file('sha256', $this->file->getPathname(), true)));

        return $this;
    }

    
    public function setContentDisposition(string $disposition, string $filename = '', string $filenameFallback = ''): static
    {
        if ('' === $filename) {
            $filename = $this->file->getFilename();
        }

        if ('' === $filenameFallback && (!preg_match('/^[\x20-\x7e]*$/', $filename) || str_contains($filename, '%'))) {
            $encoding = mb_detect_encoding($filename, null, true) ?: '8bit';

            for ($i = 0, $filenameLength = mb_strlen($filename, $encoding); $i < $filenameLength; ++$i) {
                $char = mb_substr($filename, $i, 1, $encoding);

                if ('%' === $char || \ord($char) < 32 || \ord($char) > 126) {
                    $filenameFallback .= '_';
                } else {
                    $filenameFallback .= $char;
                }
            }
        }

        $dispositionHeader = $this->headers->makeDisposition($disposition, $filename, $filenameFallback);
        $this->headers->set('Content-Disposition', $dispositionHeader);

        return $this;
    }

    
    public function prepare(Request $request): static
    {
        if ($this->isInformational() || $this->isEmpty()) {
            parent::prepare($request);

            $this->maxlen = 0;

            return $this;
        }

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', $this->file->getMimeType() ?: 'application/octet-stream');
        }

        parent::prepare($request);

        $this->offset = 0;
        $this->maxlen = -1;

        if (false === $fileSize = $this->file->getSize()) {
            return $this;
        }
        $this->headers->remove('Transfer-Encoding');
        $this->headers->set('Content-Length', $fileSize);

        if (!$this->headers->has('Accept-Ranges')) {
            
            $this->headers->set('Accept-Ranges', $request->isMethodSafe() ? 'bytes' : 'none');
        }

        if (self::$trustXSendfileTypeHeader && $request->headers->has('X-Sendfile-Type')) {
            
            $type = $request->headers->get('X-Sendfile-Type');
            $path = $this->file->getRealPath();
            
            if (false === $path) {
                $path = $this->file->getPathname();
            }
            if ('x-accel-redirect' === strtolower($type)) {
                
                
                $parts = HeaderUtils::split($request->headers->get('X-Accel-Mapping', ''), ',=');
                foreach ($parts as $part) {
                    [$pathPrefix, $location] = $part;
                    if (substr($path, 0, \strlen($pathPrefix)) === $pathPrefix) {
                        $path = $location.substr($path, \strlen($pathPrefix));
                        
                        
                        $this->headers->set($type, $path);
                        $this->maxlen = 0;
                        break;
                    }
                }
            } else {
                $this->headers->set($type, $path);
                $this->maxlen = 0;
            }
        } elseif ($request->headers->has('Range') && $request->isMethod('GET')) {
            
            if (!$request->headers->has('If-Range') || $this->hasValidIfRangeHeader($request->headers->get('If-Range'))) {
                $range = $request->headers->get('Range');

                if (str_starts_with($range, 'bytes=')) {
                    [$start, $end] = explode('-', substr($range, 6), 2) + [0];

                    $end = ('' === $end) ? $fileSize - 1 : (int) $end;

                    if ('' === $start) {
                        $start = $fileSize - $end;
                        $end = $fileSize - 1;
                    } else {
                        $start = (int) $start;
                    }

                    if ($start <= $end) {
                        $end = min($end, $fileSize - 1);
                        if ($start < 0 || $start > $end) {
                            $this->setStatusCode(416);
                            $this->headers->set('Content-Range', sprintf('bytes */%s', $fileSize));
                        } elseif ($end - $start < $fileSize - 1) {
                            $this->maxlen = $end < $fileSize ? $end - $start + 1 : -1;
                            $this->offset = $start;

                            $this->setStatusCode(206);
                            $this->headers->set('Content-Range', sprintf('bytes %s-%s/%s', $start, $end, $fileSize));
                            $this->headers->set('Content-Length', $end - $start + 1);
                        }
                    }
                }
            }
        }

        if ($request->isMethod('HEAD')) {
            $this->maxlen = 0;
        }

        return $this;
    }

    private function hasValidIfRangeHeader(?string $header): bool
    {
        if ($this->getEtag() === $header) {
            return true;
        }

        if (null === $lastModified = $this->getLastModified()) {
            return false;
        }

        return $lastModified->format('D, d M Y H:i:s').' GMT' === $header;
    }

    
    public function sendContent(): static
    {
        try {
            if (!$this->isSuccessful()) {
                return parent::sendContent();
            }

            if (0 === $this->maxlen) {
                return $this;
            }

            $out = fopen('php:
            $file = fopen($this->file->getPathname(), 'r');

            ignore_user_abort(true);

            if (0 !== $this->offset) {
                fseek($file, $this->offset);
            }

            $length = $this->maxlen;
            while ($length && !feof($file)) {
                $read = ($length > $this->chunkSize) ? $this->chunkSize : $length;
                $length -= $read;

                stream_copy_to_stream($file, $out, $read);

                if (connection_aborted()) {
                    break;
                }
            }

            fclose($out);
            fclose($file);
        } finally {
            if ($this->deleteFileAfterSend && is_file($this->file->getPathname())) {
                unlink($this->file->getPathname());
            }
        }

        return $this;
    }

    
    public function setContent(?string $content): static
    {
        if (null !== $content) {
            throw new \LogicException('The content cannot be set on a BinaryFileResponse instance.');
        }

        return $this;
    }

    
    public function getContent(): string|false
    {
        return false;
    }

    
    public static function trustXSendfileTypeHeader()
    {
        self::$trustXSendfileTypeHeader = true;
    }

    
    public function deleteFileAfterSend(bool $shouldDelete = true): static
    {
        $this->deleteFileAfterSend = $shouldDelete;

        return $this;
    }
}

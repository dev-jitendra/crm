<?php

namespace Laminas\Mail\Storage\Part;

use Laminas\Mail\Headers;
use Laminas\Mail\Storage\Part;

use function count;
use function feof;
use function fgets;
use function fopen;
use function fread;
use function fseek;
use function ftell;
use function is_resource;
use function stream_copy_to_stream;
use function trim;

use const SEEK_END;

class File extends Part
{
    
    protected $contentPos = [];
    
    protected $partPos = [];
    
    protected $fh;

    
    public function __construct(array $params)
    {
        if (empty($params['file'])) {
            throw new Exception\InvalidArgumentException('no file given in params');
        }

        if (! is_resource($params['file'])) {
            $fh = fopen($params['file'], 'r');
        } else {
            $fh = $params['file'];
        }
        if (! $fh) {
            throw new Exception\RuntimeException('could not open file');
        }
        $this->fh = $fh;
        if (isset($params['startPos'])) {
            fseek($this->fh, $params['startPos']);
        }
        $header = '';
        $endPos = $params['endPos'] ?? null;
        while (($endPos === null || ftell($this->fh) < $endPos) && trim($line = fgets($this->fh))) {
            $header .= $line;
        }

        if (isset($params['EOL'])) {
            $this->headers = Headers::fromString($header, $params['EOL']);
        } else {
            $this->headers = Headers::fromString($header);
        }

        $this->contentPos[0] = ftell($this->fh);
        if ($endPos !== null) {
            $this->contentPos[1] = $endPos;
        } else {
            fseek($this->fh, 0, SEEK_END);
            $this->contentPos[1] = ftell($this->fh);
        }
        if (! $this->isMultipart()) {
            return;
        }

        $boundary = $this->getHeaderField('content-type', 'boundary');
        if (! $boundary) {
            throw new Exception\RuntimeException('no boundary found in content type to split message');
        }

        $part = [];
        $pos  = $this->contentPos[0];
        fseek($this->fh, $pos);
        while (! feof($this->fh) && ($endPos === null || $pos < $endPos)) {
            $line = fgets($this->fh);
            if ($line === false) {
                if (feof($this->fh)) {
                    break;
                }
                throw new Exception\RuntimeException('error reading file');
            }

            $lastPos = $pos;
            $pos     = ftell($this->fh);
            $line    = trim($line);

            if ($line == '--' . $boundary) {
                if ($part) {
                    
                    $part[1]         = $lastPos;
                    $this->partPos[] = $part;
                }
                $part = [$pos];
            } elseif ($line == '--' . $boundary . '--') {
                $part[1]         = $lastPos;
                $this->partPos[] = $part;
                break;
            }
        }
        $this->countParts = count($this->partPos);
    }

    
    public function getContent($stream = null)
    {
        fseek($this->fh, $this->contentPos[0]);
        if ($stream !== null) {
            return stream_copy_to_stream($this->fh, $stream, $this->contentPos[1] - $this->contentPos[0]);
        }
        $length = $this->contentPos[1] - $this->contentPos[0];
        return $length < 1 ? '' : fread($this->fh, $length);
    }

    
    public function getSize()
    {
        return $this->contentPos[1] - $this->contentPos[0];
    }

    
    public function getPart($num)
    {
        --$num;
        if (! isset($this->partPos[$num])) {
            throw new Exception\RuntimeException('part not found');
        }

        return new static([
            'file'     => $this->fh,
            'startPos' => $this->partPos[$num][0],
            'endPos'   => $this->partPos[$num][1],
        ]);
    }
}

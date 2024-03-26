<?php

namespace Laminas\Mime;

use function array_key_exists;
use function gettype;
use function is_object;
use function is_resource;
use function is_string;
use function rewind;
use function sprintf;
use function stream_filter_append;
use function stream_filter_remove;
use function stream_get_contents;
use function stream_get_meta_data;

use const STREAM_FILTER_READ;


class Part
{
    
    public $type = Mime::TYPE_OCTETSTREAM;

    
    public $encoding = Mime::ENCODING_8BIT;

    
    public $id;

    
    public $disposition;

    
    public $filename;

    
    public $description;

    
    public $charset;

    
    public $boundary;

    
    public $location;

    
    public $language;

    
    protected $content;

    
    protected $isStream = false;

    
    protected $filters = [];

    
    public function __construct($content = '')
    {
        $this->setContent($content);
    }

    

    
    public function setType($type = Mime::TYPE_OCTETSTREAM)
    {
        $this->type = $type;
        return $this;
    }

    
    public function getType()
    {
        return $this->type;
    }

    
    public function setEncoding($encoding = Mime::ENCODING_8BIT)
    {
        $this->encoding = $encoding;
        return $this;
    }

    
    public function getEncoding()
    {
        return $this->encoding;
    }

    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    
    public function getId()
    {
        return $this->id;
    }

    
    public function setDisposition($disposition)
    {
        $this->disposition = $disposition;
        return $this;
    }

    
    public function getDisposition()
    {
        return $this->disposition;
    }

    
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    
    public function getDescription()
    {
        return $this->description;
    }

    
    public function setFileName($fileName)
    {
        $this->filename = $fileName;
        return $this;
    }

    
    public function getFileName()
    {
        return $this->filename;
    }

    
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    
    public function getCharset()
    {
        return $this->charset;
    }

    
    public function setBoundary($boundary)
    {
        $this->boundary = $boundary;
        return $this;
    }

    
    public function getBoundary()
    {
        return $this->boundary;
    }

    
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    
    public function getLocation()
    {
        return $this->location;
    }

    
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    
    public function getLanguage()
    {
        return $this->language;
    }

    
    public function setContent($content)
    {
        if (! is_string($content) && ! is_resource($content)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Content must be string or resource; received "%s"',
                is_object($content) ? $content::class : gettype($content)
            ));
        }
        $this->content = $content;
        if (is_resource($content)) {
            $this->isStream = true;
        }

        return $this;
    }

    
    public function setIsStream($isStream = false)
    {
        $this->isStream = (bool) $isStream;
        return $this;
    }

    
    public function getIsStream()
    {
        return $this->isStream;
    }

    
    public function setFilters($filters = [])
    {
        $this->filters = $filters;
        return $this;
    }

    
    public function getFilters()
    {
        return $this->filters;
    }

    
    public function isStream()
    {
        return $this->isStream;
    }

    

    
    public function getEncodedStream($EOL = Mime::LINEEND)
    {
        if (! $this->isStream) {
            throw new Exception\RuntimeException('Attempt to get a stream from a string part');
        }

        
        switch ($this->encoding) {
            case Mime::ENCODING_QUOTEDPRINTABLE:
                if (array_key_exists(Mime::ENCODING_QUOTEDPRINTABLE, $this->filters)) {
                    stream_filter_remove($this->filters[Mime::ENCODING_QUOTEDPRINTABLE]);
                }
                $filter                                        = stream_filter_append(
                    $this->content,
                    'convert.quoted-printable-encode',
                    STREAM_FILTER_READ,
                    [
                        'line-length'      => 76,
                        'line-break-chars' => $EOL,
                    ]
                );
                $this->filters[Mime::ENCODING_QUOTEDPRINTABLE] = $filter;
                if (! is_resource($filter)) {
                    throw new Exception\RuntimeException('Failed to append quoted-printable filter');
                }
                break;
            case Mime::ENCODING_BASE64:
                if (array_key_exists(Mime::ENCODING_BASE64, $this->filters)) {
                    stream_filter_remove($this->filters[Mime::ENCODING_BASE64]);
                }
                $filter                               = stream_filter_append(
                    $this->content,
                    'convert.base64-encode',
                    STREAM_FILTER_READ,
                    [
                        'line-length'      => 76,
                        'line-break-chars' => $EOL,
                    ]
                );
                $this->filters[Mime::ENCODING_BASE64] = $filter;
                if (! is_resource($filter)) {
                    throw new Exception\RuntimeException('Failed to append base64 filter');
                }
                break;
            default:
        }
        return $this->content;
    }

    
    public function getContent($EOL = Mime::LINEEND)
    {
        if ($this->isStream) {
            $encodedStream         = $this->getEncodedStream($EOL);
            $encodedStreamContents = stream_get_contents($encodedStream);
            $streamMetaData        = stream_get_meta_data($encodedStream);

            if (isset($streamMetaData['seekable']) && $streamMetaData['seekable']) {
                rewind($encodedStream);
            }

            return $encodedStreamContents;
        }
        return Mime::encode($this->content, $this->encoding, $EOL);
    }

    
    public function getRawContent()
    {
        if ($this->isStream) {
            return stream_get_contents($this->content);
        }
        return $this->content;
    }

    
    public function getHeadersArray($EOL = Mime::LINEEND)
    {
        $headers = [];

        $contentType = $this->type;
        if ($this->charset) {
            $contentType .= '; charset=' . $this->charset;
        }

        if ($this->boundary) {
            $contentType .= ';' . $EOL
                          . " boundary=\"" . $this->boundary . '"';
        }

        $headers[] = ['Content-Type', $contentType];

        if ($this->encoding) {
            $headers[] = ['Content-Transfer-Encoding', $this->encoding];
        }

        if ($this->id) {
            $headers[] = ['Content-ID', '<' . $this->id . '>'];
        }

        if ($this->disposition) {
            $disposition = $this->disposition;
            if ($this->filename) {
                $disposition .= '; filename="' . $this->filename . '"';
            }
            $headers[] = ['Content-Disposition', $disposition];
        }

        if ($this->description) {
            $headers[] = ['Content-Description', $this->description];
        }

        if ($this->location) {
            $headers[] = ['Content-Location', $this->location];
        }

        if ($this->language) {
            $headers[] = ['Content-Language', $this->language];
        }

        return $headers;
    }

    
    public function getHeaders($EOL = Mime::LINEEND)
    {
        $res = '';
        foreach ($this->getHeadersArray($EOL) as $header) {
            $res .= $header[0] . ': ' . $header[1] . $EOL;
        }

        return $res;
    }
}

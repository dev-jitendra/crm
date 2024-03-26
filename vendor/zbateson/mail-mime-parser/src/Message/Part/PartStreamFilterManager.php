<?php

namespace ZBateson\MailMimeParser\Message\Part;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\CachingStream;
use ZBateson\MailMimeParser\Stream\StreamFactory;


class PartStreamFilterManager
{
    
    protected $stream;

    
    protected $decodedStream;

    
    protected $charsetStream;

    
    private $encoding = [
        'type' => null,
        'filter' => null
    ];
    
    
    private $charset = [
        'from' => null,
        'to' => null,
        'filter' => null
    ];

    
    private $streamFactory;
    
    
    public function __construct(StreamFactory $streamFactory)
    {
        $this->streamFactory = $streamFactory;
    }

    
    public function setStream(StreamInterface $stream = null)
    {
        $this->stream = $stream;
        $this->decodedStream = null;
        $this->charsetStream = null;
    }
    
    
    private function isTransferEncodingFilterChanged($transferEncoding)
    {
        return ($transferEncoding !== $this->encoding['type']);
    }
    
    
    private function isCharsetFilterChanged($fromCharset, $toCharset)
    {
        return ($fromCharset !== $this->charset['from']
            || $toCharset !== $this->charset['to']);
    }
    
    
    protected function attachTransferEncodingFilter($transferEncoding)
    {
        if ($this->decodedStream !== null) {
            $this->encoding['type'] = $transferEncoding;
            $assign = null;
            switch ($transferEncoding) {
                case 'base64':
                    $assign = $this->streamFactory->newBase64Stream($this->decodedStream);
                    break;
                case 'x-uuencode':
                    $assign = $this->streamFactory->newUUStream($this->decodedStream);
                    break;
                case 'quoted-printable':
                    $assign = $this->streamFactory->newQuotedPrintableStream($this->decodedStream);
                    break;
            }
            if ($assign !== null) {
                $this->decodedStream = new CachingStream($assign);
            }
        }
    }
    
    
    protected function attachCharsetFilter($fromCharset, $toCharset)
    {
        if ($this->charsetStream !== null) {
            $this->charsetStream = new CachingStream($this->streamFactory->newCharsetStream(
                $this->charsetStream,
                $fromCharset,
                $toCharset
            ));
            $this->charset['from'] = $fromCharset;
            $this->charset['to'] = $toCharset;
        }
    }
    
    
    private function resetCharsetStream()
    {
        $this->charset = [
            'from' => null,
            'to' => null,
            'filter' => null
        ];
        $this->decodedStream->rewind();
        $this->charsetStream = $this->decodedStream;
    }

    
    public function reset()
    {
        $this->encoding = [
            'type' => null,
            'filter' => null
        ];
        $this->charset = [
            'from' => null,
            'to' => null,
            'filter' => null
        ];
        $this->stream->rewind();
        $this->decodedStream = $this->stream;
        $this->charsetStream = $this->stream;
    }
    
    
    public function getContentStream($transferEncoding, $fromCharset, $toCharset)
    {
        if ($this->stream === null) {
            return null;
        }
        if (empty($fromCharset) || empty($toCharset)) {
            return $this->getBinaryStream($transferEncoding);
        }
        if ($this->charsetStream === null
            || $this->isTransferEncodingFilterChanged($transferEncoding)
            || $this->isCharsetFilterChanged($fromCharset, $toCharset)) {
            if ($this->charsetStream === null
                || $this->isTransferEncodingFilterChanged($transferEncoding)) {
                $this->reset();
                $this->attachTransferEncodingFilter($transferEncoding);
            }
            $this->resetCharsetStream();
            $this->attachCharsetFilter($fromCharset, $toCharset);
        }
        $this->charsetStream->rewind();
        return $this->charsetStream;
    }

    
    public function getBinaryStream($transferEncoding)
    {
        if ($this->stream === null) {
            return null;
        }
        if ($this->decodedStream === null
            || $this->isTransferEncodingFilterChanged($transferEncoding)) {
            $this->reset();
            $this->attachTransferEncodingFilter($transferEncoding);
        }
        $this->decodedStream->rewind();
        return $this->decodedStream;
    }
}

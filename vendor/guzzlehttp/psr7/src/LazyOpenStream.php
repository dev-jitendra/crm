<?php

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;


class LazyOpenStream implements StreamInterface
{
    use StreamDecoratorTrait;

    
    private $filename;

    
    private $mode;

    
    public function __construct($filename, $mode)
    {
        $this->filename = $filename;
        $this->mode = $mode;
    }

    
    protected function createStream()
    {
        return Utils::streamFor(Utils::tryFopen($this->filename, $this->mode));
    }
}

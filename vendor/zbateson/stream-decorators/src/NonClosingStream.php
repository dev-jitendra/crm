<?php

namespace ZBateson\StreamDecorators;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\StreamDecoratorTrait;


class NonClosingStream implements StreamInterface
{
    use StreamDecoratorTrait;

    
    public function close()
    {
        $this->stream = null;
    }

    
    public function detach()
    {
        $this->stream = null;
    }
}

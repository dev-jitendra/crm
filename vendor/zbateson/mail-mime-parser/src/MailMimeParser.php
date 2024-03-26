<?php

namespace ZBateson\MailMimeParser;

use GuzzleHttp\Psr7;


class MailMimeParser
{
    
    const DEFAULT_CHARSET = 'UTF-8';

    
    protected $di;
    
    
    public function __construct(Container $di = null)
    {
        if ($di === null) {
            $di = new Container();
        }
        $this->di = $di;
    }

    
    public function parse($handleOrString)
    {
        $stream = Psr7\stream_for($handleOrString);
        $copy = Psr7\stream_for(fopen('php:

        Psr7\copy_to_stream($stream, $copy);
        $copy->rewind();

        
        $stream->detach();
        $parser = $this->di->newMessageParser();
        return $parser->parse($copy);
    }
}

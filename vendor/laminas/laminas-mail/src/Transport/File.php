<?php

namespace Laminas\Mail\Transport;

use Laminas\Mail\Message;

use function file_put_contents;
use function sprintf;

use const DIRECTORY_SEPARATOR;


class File implements TransportInterface
{
    
    protected $options;

    
    protected $lastFile;

    
    public function __construct(?FileOptions $options = null)
    {
        if (! $options instanceof FileOptions) {
            $options = new FileOptions();
        }
        $this->setOptions($options);
    }

    
    public function getOptions()
    {
        return $this->options;
    }

    
    public function setOptions(FileOptions $options)
    {
        $this->options = $options;
    }

    
    public function send(Message $message)
    {
        $options  = $this->options;
        $filename = $options->getCallback()($this);
        $file     = $options->getPath() . DIRECTORY_SEPARATOR . $filename;
        $email    = $message->toString();

        if (false === file_put_contents($file, $email)) {
            throw new Exception\RuntimeException(sprintf(
                'Unable to write mail to file (directory "%s")',
                $options->getPath()
            ));
        }

        $this->lastFile = $file;
    }

    
    public function getLastFile()
    {
        return $this->lastFile;
    }
}

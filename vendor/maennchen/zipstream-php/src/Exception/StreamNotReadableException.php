<?php
declare(strict_types=1);

namespace ZipStream\Exception;

use ZipStream\Exception;


class StreamNotReadableException extends Exception
{
    
    public function __construct(string $fileName)
    {
        parent::__construct("The stream for $fileName could not be read.");
    }
}

<?php

declare(strict_types=1);

namespace Laminas\Stdlib\StringWrapper;

use Laminas\Stdlib\Exception;
use Laminas\Stdlib\StringUtils;

use function in_array;
use function strlen;
use function strpos;
use function strtoupper;
use function substr;

class Native extends AbstractStringWrapper
{
    
    protected $encoding = 'ASCII';

    
    public static function isSupported($encoding, $convertEncoding = null)
    {
        $encodingUpper      = strtoupper($encoding);
        $supportedEncodings = static::getSupportedEncodings();

        if (! in_array($encodingUpper, $supportedEncodings)) {
            return false;
        }

        
        if ($convertEncoding !== null && $encodingUpper !== strtoupper($convertEncoding)) {
            return false;
        }

        return true;
    }

    
    public static function getSupportedEncodings()
    {
        return StringUtils::getSingleByteEncodings();
    }

    
    public function setEncoding($encoding, $convertEncoding = null)
    {
        $supportedEncodings = static::getSupportedEncodings();

        $encodingUpper = strtoupper($encoding);
        if (! in_array($encodingUpper, $supportedEncodings)) {
            throw new Exception\InvalidArgumentException(
                'Wrapper doesn\'t support character encoding "' . $encoding . '"'
            );
        }

        if (null !== $convertEncoding && $encodingUpper !== strtoupper($convertEncoding)) {
            $this->convertEncoding = $encodingUpper;
        }

        if ($convertEncoding !== null) {
            if ($encodingUpper !== strtoupper($convertEncoding)) {
                throw new Exception\InvalidArgumentException(
                    'Wrapper doesn\'t support to convert between character encodings'
                );
            }

            $this->convertEncoding = $encodingUpper;
        } else {
            $this->convertEncoding = null;
        }
        $this->encoding = $encodingUpper;

        return $this;
    }

    
    public function strlen($str)
    {
        return strlen($str);
    }

    
    public function substr($str, $offset = 0, $length = null)
    {
        return substr($str, $offset, $length);
    }

    
    public function strpos($haystack, $needle, $offset = 0)
    {
        return strpos($haystack, $needle, $offset);
    }
}

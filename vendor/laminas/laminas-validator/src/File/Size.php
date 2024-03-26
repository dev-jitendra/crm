<?php

namespace Laminas\Validator\File;

use Laminas\Stdlib\ErrorHandler;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Traversable;

use function array_shift;
use function filesize;
use function func_get_args;
use function func_num_args;
use function is_numeric;
use function is_readable;
use function is_string;
use function round;
use function sprintf;
use function strtoupper;
use function substr;
use function trim;


class Size extends AbstractValidator
{
    use FileInformationTrait;

    
    public const TOO_BIG   = 'fileSizeTooBig';
    public const TOO_SMALL = 'fileSizeTooSmall';
    public const NOT_FOUND = 'fileSizeNotFound';

    
    protected $messageTemplates = [
        self::TOO_BIG   => "Maximum allowed size for file is '%max%' but '%size%' detected",
        self::TOO_SMALL => "Minimum expected size for file is '%min%' but '%size%' detected",
        self::NOT_FOUND => 'File is not readable or does not exist',
    ];

    
    protected $messageVariables = [
        'min'  => ['options' => 'min'],
        'max'  => ['options' => 'max'],
        'size' => 'size',
    ];

    
    protected $size;

    
    protected $options = [
        'min'           => null, 
        'max'           => null, 
        'useByteString' => true, 
    ];

    
    public function __construct($options = null)
    {
        if (is_string($options) || is_numeric($options)) {
            $options = ['max' => $options];
        }

        if (1 < func_num_args()) {
            $argv = func_get_args();
            array_shift($argv);
            $options['max'] = array_shift($argv);
            if (! empty($argv)) {
                $options['useByteString'] = array_shift($argv);
            }
        }

        parent::__construct($options);
    }

    
    public function useByteString($byteString = true)
    {
        $this->options['useByteString'] = (bool) $byteString;
        return $this;
    }

    
    public function getByteString()
    {
        return $this->options['useByteString'];
    }

    
    public function getMin($raw = false)
    {
        $min = $this->options['min'];
        if (! $raw && $this->getByteString()) {
            $min = $this->toByteString($min);
        }

        return $min;
    }

    
    public function setMin($min)
    {
        if (! is_string($min) && ! is_numeric($min)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        $min = (int) $this->fromByteString($min);
        $max = $this->getMax(true);
        if (($max !== null) && ($min > $max)) {
            throw new Exception\InvalidArgumentException(
                "The minimum must be less than or equal to the maximum file size, but $min > $max"
            );
        }

        $this->options['min'] = $min;
        return $this;
    }

    
    public function getMax($raw = false)
    {
        $max = $this->options['max'];
        if (! $raw && $this->getByteString()) {
            $max = $this->toByteString($max);
        }

        return $max;
    }

    
    public function setMax($max)
    {
        if (! is_string($max) && ! is_numeric($max)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        $max = (int) $this->fromByteString($max);
        $min = $this->getMin(true);
        if (($min !== null) && ($max < $min)) {
            throw new Exception\InvalidArgumentException(
                "The maximum must be greater than or equal to the minimum file size, but $max < $min"
            );
        }

        $this->options['max'] = $max;
        return $this;
    }

    
    protected function getSize()
    {
        return $this->size;
    }

    
    protected function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    
    public function isValid($value, $file = null)
    {
        $fileInfo = $this->getFileInfo($value, $file);

        $this->setValue($fileInfo['filename']);

        
        if (empty($fileInfo['file']) || false === is_readable($fileInfo['file'])) {
            $this->error(self::NOT_FOUND);
            return false;
        }

        
        ErrorHandler::start();
        $size = sprintf('%u', filesize($fileInfo['file']));
        ErrorHandler::stop();
        $this->size = $size;

        
        $min = $this->getMin(true);
        $max = $this->getMax(true);
        if (($min !== null) && ($size < $min)) {
            if ($this->getByteString()) {
                $this->options['min'] = $this->toByteString($min);
                $this->size           = $this->toByteString($size);
                $this->error(self::TOO_SMALL);
                $this->options['min'] = $min;
                $this->size           = $size;
            } else {
                $this->error(self::TOO_SMALL);
            }
        }

        
        if (($max !== null) && ($max < $size)) {
            if ($this->getByteString()) {
                $this->options['max'] = $this->toByteString($max);
                $this->size           = $this->toByteString($size);
                $this->error(self::TOO_BIG);
                $this->options['max'] = $max;
                $this->size           = $size;
            } else {
                $this->error(self::TOO_BIG);
            }
        }

        if ($this->getMessages()) {
            return false;
        }

        return true;
    }

    
    protected function toByteString($size)
    {
        $sizes = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        for ($i = 0; $size >= 1024 && $i < 9; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . $sizes[$i];
    }

    
    protected function fromByteString($size)
    {
        if (is_numeric($size)) {
            return (int) $size;
        }

        $type = trim(substr($size, -2, 1));

        $value = substr($size, 0, -1);
        if (! is_numeric($value)) {
            $value = trim(substr($value, 0, -1));
        }

        switch (strtoupper($type)) {
            case 'Y':
                $value *= 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024;
                break;
            case 'Z':
                $value *= 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024;
                break;
            case 'E':
                $value *= 1024 * 1024 * 1024 * 1024 * 1024 * 1024;
                break;
            case 'P':
                $value *= 1024 * 1024 * 1024 * 1024 * 1024;
                break;
            case 'T':
                $value *= 1024 * 1024 * 1024 * 1024;
                break;
            case 'G':
                $value *= 1024 * 1024 * 1024;
                break;
            case 'M':
                $value *= 1024 * 1024;
                break;
            case 'K':
                $value *= 1024;
                break;
            default:
                break;
        }

        return $value;
    }
}

<?php

namespace Laminas\Validator\File;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Psr\Http\Message\UploadedFileInterface;
use Traversable;

use function array_key_exists;
use function array_shift;
use function count;
use function dirname;
use function func_get_args;
use function func_num_args;
use function is_array;
use function is_numeric;
use function is_string;

use const DIRECTORY_SEPARATOR;


class Count extends AbstractValidator
{
    
    public const TOO_MANY = 'fileCountTooMany';
    public const TOO_FEW  = 'fileCountTooFew';
    

    
    protected $messageTemplates = [
        self::TOO_MANY => "Too many files, maximum '%max%' are allowed but '%count%' are given",
        self::TOO_FEW  => "Too few files, minimum '%min%' are expected but '%count%' are given",
    ];

    
    protected $messageVariables = [
        'min'   => ['options' => 'min'],
        'max'   => ['options' => 'max'],
        'count' => 'count',
    ];

    
    protected $count;

    
    protected $files;

    
    protected $options = [
        'min' => null, 
        'max' => null, 
    ];

    
    public function __construct($options = null)
    {
        if (1 < func_num_args()) {
            $args    = func_get_args();
            $options = [
                'min' => array_shift($args),
                'max' => array_shift($args),
            ];
        }

        if (is_string($options) || is_numeric($options)) {
            $options = ['max' => $options];
        }

        parent::__construct($options);
    }

    
    public function getMin()
    {
        return $this->options['min'];
    }

    
    public function setMin($min)
    {
        if (is_array($min) && isset($min['min'])) {
            $min = $min['min'];
        }

        if (! is_numeric($min)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        $min = (int) $min;
        if (($this->getMax() !== null) && ($min > $this->getMax())) {
            throw new Exception\InvalidArgumentException(
                "The minimum must be less than or equal to the maximum file count, but {$min} > {$this->getMax()}"
            );
        }

        $this->options['min'] = $min;
        return $this;
    }

    
    public function getMax()
    {
        return $this->options['max'];
    }

    
    public function setMax($max)
    {
        if (is_array($max) && isset($max['max'])) {
            $max = $max['max'];
        }

        if (! is_numeric($max)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        $max = (int) $max;
        if (($this->getMin() !== null) && ($max < $this->getMin())) {
            throw new Exception\InvalidArgumentException(
                "The maximum must be greater than or equal to the minimum file count, but {$max} < {$this->getMin()}"
            );
        }

        $this->options['max'] = $max;
        return $this;
    }

    
    public function addFile($file)
    {
        if (is_string($file)) {
            $file = [$file];
        }

        if (is_array($file)) {
            foreach ($file as $name) {
                if (! isset($this->files[$name]) && ! empty($name)) {
                    $this->files[$name] = $name;
                }
            }
        }

        if ($file instanceof UploadedFileInterface && is_string($file->getClientFilename())) {
            $this->files[(string) $file->getClientFilename()] = $file->getClientFilename();
        }

        return $this;
    }

    
    public function isValid($value, $file = null)
    {
        if ($this->isUploadedFilterInterface($value)) {
            $this->addFile($value);
        } elseif ($file !== null) {
            if (! array_key_exists('destination', $file)) {
                $file['destination'] = dirname($value);
            }

            if (array_key_exists('tmp_name', $file)) {
                $value = $file['destination'] . DIRECTORY_SEPARATOR . $file['name'];
            }
        }

        if (($file === null) || ! empty($file['tmp_name'])) {
            $this->addFile($value);
        }

        $this->count = count($this->files);

        if (($this->getMax() !== null) && ($this->count > $this->getMax())) {
            return $this->throwError($file, self::TOO_MANY);
        }

        if (($this->getMin() !== null) && ($this->count < $this->getMin())) {
            return $this->throwError($file, self::TOO_FEW);
        }

        return true;
    }

    
    protected function throwError($file, $errorType)
    {
        if ($file !== null) {
            if (is_array($file)) {
                if (array_key_exists('name', $file)) {
                    $this->value = $file['name'];
                }
            } elseif (is_string($file)) {
                $this->value = $file;
            }
        }

        $this->error($errorType);
        return false;
    }

    
    private function isUploadedFilterInterface($value)
    {
        if ($value instanceof UploadedFileInterface) {
            return true;
        }

        return false;
    }
}

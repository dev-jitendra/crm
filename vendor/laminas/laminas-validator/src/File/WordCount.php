<?php

namespace Laminas\Validator\File;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Traversable;

use function array_shift;
use function file_get_contents;
use function func_get_args;
use function func_num_args;
use function is_array;
use function is_numeric;
use function is_readable;
use function is_string;
use function str_word_count;


class WordCount extends AbstractValidator
{
    use FileInformationTrait;

    
    public const TOO_MUCH  = 'fileWordCountTooMuch';
    public const TOO_LESS  = 'fileWordCountTooLess';
    public const NOT_FOUND = 'fileWordCountNotFound';

    
    protected $messageTemplates = [
        self::TOO_MUCH  => "Too many words, maximum '%max%' are allowed but '%count%' were counted",
        self::TOO_LESS  => "Too few words, minimum '%min%' are expected but '%count%' were counted",
        self::NOT_FOUND => 'File is not readable or does not exist',
    ];

    
    protected $messageVariables = [
        'min'   => ['options' => 'min'],
        'max'   => ['options' => 'max'],
        'count' => 'count',
    ];

    
    protected $count;

    
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
                "The minimum must be less than or equal to the maximum word count, but $min > {$this->getMax()}"
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
                "The maximum must be greater than or equal to the minimum word count, but $max < {$this->getMin()}"
            );
        }

        $this->options['max'] = $max;
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

        $content     = file_get_contents($fileInfo['file']);
        $this->count = str_word_count($content);
        if (($this->getMax() !== null) && ($this->count > $this->getMax())) {
            $this->error(self::TOO_MUCH);
            return false;
        }

        if (($this->getMin() !== null) && ($this->count < $this->getMin())) {
            $this->error(self::TOO_LESS);
            return false;
        }

        return true;
    }
}

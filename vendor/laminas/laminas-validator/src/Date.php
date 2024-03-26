<?php

declare(strict_types=1);

namespace Laminas\Validator;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Traversable;

use function array_shift;
use function func_get_args;
use function gettype;
use function implode;
use function is_array;
use function iterator_to_array;


class Date extends AbstractValidator
{
    
    public const INVALID      = 'dateInvalid';
    public const INVALID_DATE = 'dateInvalidDate';
    public const FALSEFORMAT  = 'dateFalseFormat';
    

    
    public const FORMAT_DEFAULT = 'Y-m-d';

    
    protected $messageTemplates = [
        self::INVALID      => 'Invalid type given. String, integer, array or DateTime expected',
        self::INVALID_DATE => 'The input does not appear to be a valid date',
        self::FALSEFORMAT  => "The input does not fit the date format '%format%'",
    ];

    
    protected $messageVariables = [
        'format' => 'format',
    ];

    
    protected $format = self::FORMAT_DEFAULT;

    
    protected $strict = false;

    
    public function __construct($options = [])
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        } elseif (! is_array($options)) {
            $options        = func_get_args();
            $temp['format'] = array_shift($options);
            $options        = $temp;
        }

        parent::__construct($options);
    }

    
    public function getFormat()
    {
        return $this->format;
    }

    
    public function setFormat($format = self::FORMAT_DEFAULT)
    {
        $this->format = empty($format) ? self::FORMAT_DEFAULT : $format;
        return $this;
    }

    public function setStrict(bool $strict): self
    {
        $this->strict = $strict;
        return $this;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    
    public function isValid($value)
    {
        $this->setValue($value);

        $date = $this->convertToDateTime($value);
        if (! $date) {
            $this->error(self::INVALID_DATE);
            return false;
        }

        if ($this->isStrict() && $date->format($this->getFormat()) !== $value) {
            $this->error(self::FALSEFORMAT);
            return false;
        }

        return true;
    }

    
    protected function convertToDateTime($param, $addErrors = true)
    {
        if ($param instanceof DateTime) {
            return $param;
        }

        if ($param instanceof DateTimeImmutable) {
            return DateTime::createFromImmutable($param);
        }

        $type = gettype($param);
        switch ($type) {
            case 'string':
                return $this->convertString($param, $addErrors);
            case 'integer':
                return $this->convertInteger($param);
            case 'double':
                return $this->convertDouble($param);
            case 'array':
                return $this->convertArray($param, $addErrors);
        }

        if ($addErrors) {
            $this->error(self::INVALID);
        }

        return false;
    }

    
    protected function convertInteger($value)
    {
        return DateTime::createFromFormat('U', (string) $value);
    }

    
    protected function convertDouble($value)
    {
        return DateTime::createFromFormat('U', (string) $value);
    }

    
    protected function convertString($value, $addErrors = true)
    {
        $date = DateTime::createFromFormat($this->format, $value);

        
        
        $errors = DateTime::getLastErrors();
        if ($errors === false) {
            return $date;
        }

        if ($errors['warning_count'] > 0) {
            if ($addErrors) {
                $this->error(self::FALSEFORMAT);
            }
            return false;
        }

        return $date;
    }

    
    protected function convertArray(array $value, $addErrors = true)
    {
        return $this->convertString(implode('-', $value), $addErrors);
    }
}

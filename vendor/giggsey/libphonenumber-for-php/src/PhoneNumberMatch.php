<?php

namespace libphonenumber;

class PhoneNumberMatch
{
    
    private $start;

    
    private $rawString;

    
    private $number;

    
    public function __construct($start, $rawString, PhoneNumber $number)
    {
        if ($start < 0) {
            throw new \InvalidArgumentException('Start index must be >= 0.');
        }

        if ($rawString === null) {
            throw new \InvalidArgumentException('$rawString must be a string');
        }

        $this->start = $start;
        $this->rawString = $rawString;
        $this->number = $number;
    }

    
    public function number()
    {
        return $this->number;
    }

    
    public function start()
    {
        return $this->start;
    }

    
    public function end()
    {
        return $this->start + \mb_strlen($this->rawString);
    }

    
    public function rawString()
    {
        return $this->rawString;
    }

    public function __toString()
    {
        return "PhoneNumberMatch [{$this->start()},{$this->end()}) {$this->rawString}";
    }
}

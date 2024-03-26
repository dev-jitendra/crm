<?php

namespace libphonenumber;


class PhoneNumberDesc
{
    protected $hasNationalNumberPattern = false;
    protected $nationalNumberPattern = '';
    protected $hasExampleNumber = false;
    protected $exampleNumber = '';
    
    protected $possibleLength;
    
    protected $possibleLengthLocalOnly;

    public function __construct()
    {
        $this->clear();
    }

    
    public function clear()
    {
        $this->clearNationalNumberPattern();
        $this->clearPossibleLength();
        $this->clearPossibleLengthLocalOnly();
        $this->clearExampleNumber();

        return $this;
    }

    
    public function getPossibleLength()
    {
        return $this->possibleLength;
    }

    
    public function setPossibleLength($possibleLength)
    {
        $this->possibleLength = $possibleLength;
    }

    public function addPossibleLength($possibleLength)
    {
        if (!in_array($possibleLength, $this->possibleLength)) {
            $this->possibleLength[] = $possibleLength;
        }
    }

    public function clearPossibleLength()
    {
        $this->possibleLength = array();
    }

    
    public function getPossibleLengthLocalOnly()
    {
        return $this->possibleLengthLocalOnly;
    }

    
    public function setPossibleLengthLocalOnly($possibleLengthLocalOnly)
    {
        $this->possibleLengthLocalOnly = $possibleLengthLocalOnly;
    }

    public function addPossibleLengthLocalOnly($possibleLengthLocalOnly)
    {
        if (!in_array($possibleLengthLocalOnly, $this->possibleLengthLocalOnly)) {
            $this->possibleLengthLocalOnly[] = $possibleLengthLocalOnly;
        }
    }

    public function clearPossibleLengthLocalOnly()
    {
        $this->possibleLengthLocalOnly = array();
    }

    
    public function hasNationalNumberPattern()
    {
        return $this->hasNationalNumberPattern;
    }

    
    public function getNationalNumberPattern()
    {
        return $this->nationalNumberPattern;
    }

    
    public function setNationalNumberPattern($value)
    {
        $this->hasNationalNumberPattern = true;
        $this->nationalNumberPattern = $value;

        return $this;
    }

    
    public function clearNationalNumberPattern()
    {
        $this->hasNationalNumberPattern = false;
        $this->nationalNumberPattern = '';
        return $this;
    }

    
    public function hasExampleNumber()
    {
        return $this->hasExampleNumber;
    }

    
    public function getExampleNumber()
    {
        return $this->exampleNumber;
    }

    
    public function setExampleNumber($value)
    {
        $this->hasExampleNumber = true;
        $this->exampleNumber = $value;

        return $this;
    }

    
    public function clearExampleNumber()
    {
        $this->hasExampleNumber = false;
        $this->exampleNumber = '';

        return $this;
    }

    
    public function mergeFrom(PhoneNumberDesc $other)
    {
        if ($other->hasNationalNumberPattern()) {
            $this->setNationalNumberPattern($other->getNationalNumberPattern());
        }
        if ($other->hasExampleNumber()) {
            $this->setExampleNumber($other->getExampleNumber());
        }
        $this->setPossibleLength($other->getPossibleLength());
        $this->setPossibleLengthLocalOnly($other->getPossibleLengthLocalOnly());

        return $this;
    }

    
    public function exactlySameAs(PhoneNumberDesc $other)
    {
        return $this->nationalNumberPattern === $other->nationalNumberPattern &&
        $this->exampleNumber === $other->exampleNumber;
    }

    
    public function toArray()
    {
        $data = array();
        if ($this->hasNationalNumberPattern()) {
            $data['NationalNumberPattern'] = $this->getNationalNumberPattern();
        }
        if ($this->hasExampleNumber()) {
            $data['ExampleNumber'] = $this->getExampleNumber();
        }

        $data['PossibleLength'] = $this->getPossibleLength();
        $data['PossibleLengthLocalOnly'] = $this->getPossibleLengthLocalOnly();

        return $data;
    }

    
    public function fromArray(array $input)
    {
        if (isset($input['NationalNumberPattern']) && $input['NationalNumberPattern'] != '') {
            $this->setNationalNumberPattern($input['NationalNumberPattern']);
        }
        if (isset($input['ExampleNumber']) && $input['NationalNumberPattern'] != '') {
            $this->setExampleNumber($input['ExampleNumber']);
        }
        $this->setPossibleLength($input['PossibleLength']);
        $this->setPossibleLengthLocalOnly($input['PossibleLengthLocalOnly']);

        return $this;
    }
}

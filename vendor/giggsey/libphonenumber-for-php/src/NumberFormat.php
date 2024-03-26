<?php

namespace libphonenumber;


class NumberFormat
{
    
    protected $pattern;
    
    protected $hasPattern = false;

    
    protected $format;

    
    protected $hasFormat = false;

    
    protected $leadingDigitsPattern = array();

    
    protected $nationalPrefixFormattingRule = '';

    
    protected $hasNationalPrefixFormattingRule = false;
    
    protected $nationalPrefixOptionalWhenFormatting = false;

    
    protected $hasNationalPrefixOptionalWhenFormatting = false;

    
    protected $domesticCarrierCodeFormattingRule = '';

    
    protected $hasDomesticCarrierCodeFormattingRule = false;

    public function __construct()
    {
        $this->clear();
    }

    
    public function clear()
    {
        $this->hasPattern = false;
        $this->pattern = null;

        $this->hasFormat = false;
        $this->format = null;

        $this->leadingDigitsPattern = array();

        $this->hasNationalPrefixFormattingRule = false;
        $this->nationalPrefixFormattingRule = '';

        $this->hasNationalPrefixOptionalWhenFormatting = false;
        $this->nationalPrefixOptionalWhenFormatting = false;

        $this->hasDomesticCarrierCodeFormattingRule = false;
        $this->domesticCarrierCodeFormattingRule = '';

        return $this;
    }

    
    public function hasPattern()
    {
        return $this->hasPattern;
    }

    
    public function getPattern()
    {
        return $this->pattern;
    }

    
    public function setPattern($value)
    {
        $this->hasPattern = true;
        $this->pattern = $value;

        return $this;
    }

    
    public function hasNationalPrefixOptionalWhenFormatting()
    {
        return $this->hasNationalPrefixOptionalWhenFormatting;
    }

    
    public function getNationalPrefixOptionalWhenFormatting()
    {
        return $this->nationalPrefixOptionalWhenFormatting;
    }

    
    public function setNationalPrefixOptionalWhenFormatting($nationalPrefixOptionalWhenFormatting)
    {
        $this->hasNationalPrefixOptionalWhenFormatting = true;
        $this->nationalPrefixOptionalWhenFormatting = $nationalPrefixOptionalWhenFormatting;
    }

    
    public function hasFormat()
    {
        return $this->hasFormat;
    }

    
    public function getFormat()
    {
        return $this->format;
    }

    
    public function setFormat($value)
    {
        $this->hasFormat = true;
        $this->format = $value;

        return $this;
    }

    
    public function leadingDigitPatterns()
    {
        return $this->leadingDigitsPattern;
    }

    
    public function leadingDigitsPatternSize()
    {
        return count($this->leadingDigitsPattern);
    }

    
    public function getLeadingDigitsPattern($index)
    {
        return $this->leadingDigitsPattern[$index];
    }

    
    public function addLeadingDigitsPattern($value)
    {
        $this->leadingDigitsPattern[] = $value;

        return $this;
    }

    
    public function hasNationalPrefixFormattingRule()
    {
        return $this->hasNationalPrefixFormattingRule;
    }

    
    public function getNationalPrefixFormattingRule()
    {
        return $this->nationalPrefixFormattingRule;
    }

    
    public function setNationalPrefixFormattingRule($value)
    {
        $this->hasNationalPrefixFormattingRule = true;
        $this->nationalPrefixFormattingRule = (string)$value;

        return $this;
    }

    
    public function clearNationalPrefixFormattingRule()
    {
        $this->nationalPrefixFormattingRule = '';

        return $this;
    }

    
    public function hasDomesticCarrierCodeFormattingRule()
    {
        return $this->hasDomesticCarrierCodeFormattingRule;
    }

    
    public function getDomesticCarrierCodeFormattingRule()
    {
        return $this->domesticCarrierCodeFormattingRule;
    }

    
    public function setDomesticCarrierCodeFormattingRule($value)
    {
        $this->hasDomesticCarrierCodeFormattingRule = true;
        $this->domesticCarrierCodeFormattingRule = (string)$value;

        return $this;
    }

    
    public function mergeFrom(NumberFormat $other)
    {
        if ($other->hasPattern()) {
            $this->setPattern($other->getPattern());
        }
        if ($other->hasFormat()) {
            $this->setFormat($other->getFormat());
        }
        $leadingDigitsPatternSize = $other->leadingDigitsPatternSize();
        for ($i = 0; $i < $leadingDigitsPatternSize; $i++) {
            $this->addLeadingDigitsPattern($other->getLeadingDigitsPattern($i));
        }
        if ($other->hasNationalPrefixFormattingRule()) {
            $this->setNationalPrefixFormattingRule($other->getNationalPrefixFormattingRule());
        }
        if ($other->hasDomesticCarrierCodeFormattingRule()) {
            $this->setDomesticCarrierCodeFormattingRule($other->getDomesticCarrierCodeFormattingRule());
        }
        if ($other->hasNationalPrefixOptionalWhenFormatting()) {
            $this->setNationalPrefixOptionalWhenFormatting($other->getNationalPrefixOptionalWhenFormatting());
        }

        return $this;
    }

    
    public function toArray()
    {
        $output = array();
        $output['pattern'] = $this->getPattern();
        $output['format'] = $this->getFormat();

        $output['leadingDigitsPatterns'] = $this->leadingDigitPatterns();

        if ($this->hasNationalPrefixFormattingRule()) {
            $output['nationalPrefixFormattingRule'] = $this->getNationalPrefixFormattingRule();
        }

        if ($this->hasDomesticCarrierCodeFormattingRule()) {
            $output['domesticCarrierCodeFormattingRule'] = $this->getDomesticCarrierCodeFormattingRule();
        }

        if ($this->hasNationalPrefixOptionalWhenFormatting()) {
            $output['nationalPrefixOptionalWhenFormatting'] = $this->getNationalPrefixOptionalWhenFormatting();
        }

        return $output;
    }

    
    public function fromArray(array $input)
    {
        $this->setPattern($input['pattern']);
        $this->setFormat($input['format']);
        foreach ($input['leadingDigitsPatterns'] as $leadingDigitsPattern) {
            $this->addLeadingDigitsPattern($leadingDigitsPattern);
        }

        if (isset($input['nationalPrefixFormattingRule']) && $input['nationalPrefixFormattingRule'] !== '') {
            $this->setNationalPrefixFormattingRule($input['nationalPrefixFormattingRule']);
        }
        if (isset($input['domesticCarrierCodeFormattingRule']) && $input['domesticCarrierCodeFormattingRule'] !== '') {
            $this->setDomesticCarrierCodeFormattingRule($input['domesticCarrierCodeFormattingRule']);
        }

        if (isset($input['nationalPrefixOptionalWhenFormatting'])) {
            $this->setNationalPrefixOptionalWhenFormatting($input['nationalPrefixOptionalWhenFormatting']);
        }
    }
}

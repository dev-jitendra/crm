<?php

namespace libphonenumber;

class PhoneNumber implements \Serializable
{
    
    protected $countryCode;
    
    protected $nationalNumber;
    
    protected $extension;
    
    protected $italianLeadingZero;
    
    protected $rawInput;
    
    protected $countryCodeSource = CountryCodeSource::UNSPECIFIED;
    
    protected $preferredDomesticCarrierCode;
    
    protected $hasNumberOfLeadingZeros = false;
    
    protected $numberOfLeadingZeros = 1;

    
    public function clear()
    {
        $this->clearCountryCode();
        $this->clearNationalNumber();
        $this->clearExtension();
        $this->clearItalianLeadingZero();
        $this->clearNumberOfLeadingZeros();
        $this->clearRawInput();
        $this->clearCountryCodeSource();
        $this->clearPreferredDomesticCarrierCode();
        return $this;
    }

    
    public function clearCountryCode()
    {
        $this->countryCode = null;
        return $this;
    }

    
    public function clearNationalNumber()
    {
        $this->nationalNumber = null;
        return $this;
    }

    
    public function clearExtension()
    {
        $this->extension = null;
        return $this;
    }

    
    public function clearItalianLeadingZero()
    {
        $this->italianLeadingZero = null;
        return $this;
    }

    
    public function clearNumberOfLeadingZeros()
    {
        $this->hasNumberOfLeadingZeros = false;
        $this->numberOfLeadingZeros = 1;
        return $this;
    }

    
    public function clearRawInput()
    {
        $this->rawInput = null;
        return $this;
    }

    
    public function clearCountryCodeSource()
    {
        $this->countryCodeSource = CountryCodeSource::UNSPECIFIED;
        return $this;
    }

    
    public function clearPreferredDomesticCarrierCode()
    {
        $this->preferredDomesticCarrierCode = null;
        return $this;
    }

    
    public function mergeFrom(PhoneNumber $other)
    {
        if ($other->hasCountryCode()) {
            $this->setCountryCode($other->getCountryCode());
        }
        if ($other->hasNationalNumber()) {
            $this->setNationalNumber($other->getNationalNumber());
        }
        if ($other->hasExtension()) {
            $this->setExtension($other->getExtension());
        }
        if ($other->hasItalianLeadingZero()) {
            $this->setItalianLeadingZero($other->isItalianLeadingZero());
        }
        if ($other->hasNumberOfLeadingZeros()) {
            $this->setNumberOfLeadingZeros($other->getNumberOfLeadingZeros());
        }
        if ($other->hasRawInput()) {
            $this->setRawInput($other->getRawInput());
        }
        if ($other->hasCountryCodeSource()) {
            $this->setCountryCodeSource($other->getCountryCodeSource());
        }
        if ($other->hasPreferredDomesticCarrierCode()) {
            $this->setPreferredDomesticCarrierCode($other->getPreferredDomesticCarrierCode());
        }
        return $this;
    }

    
    public function hasCountryCode()
    {
        return $this->countryCode !== null;
    }

    
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    
    public function setCountryCode($value)
    {
        $this->countryCode = (int) $value;
        return $this;
    }

    
    public function hasNationalNumber()
    {
        return $this->nationalNumber !== null;
    }

    
    public function getNationalNumber()
    {
        return $this->nationalNumber;
    }

    
    public function setNationalNumber($value)
    {
        $this->nationalNumber = (string) $value;
        return $this;
    }

    
    public function hasExtension()
    {
        return $this->extension !== null;
    }

    
    public function getExtension()
    {
        return $this->extension;
    }

    
    public function setExtension($value)
    {
        $this->extension = (string) $value;
        return $this;
    }

    
    public function hasItalianLeadingZero()
    {
        return $this->italianLeadingZero !== null;
    }

    
    public function setItalianLeadingZero($value)
    {
        $this->italianLeadingZero = (bool) $value;
        return $this;
    }

    
    public function isItalianLeadingZero()
    {
        return $this->italianLeadingZero;
    }

    
    public function hasNumberOfLeadingZeros()
    {
        return $this->hasNumberOfLeadingZeros;
    }

    
    public function getNumberOfLeadingZeros()
    {
        return $this->numberOfLeadingZeros;
    }

    
    public function setNumberOfLeadingZeros($value)
    {
        $this->hasNumberOfLeadingZeros = true;
        $this->numberOfLeadingZeros = (int) $value;
        return $this;
    }

    
    public function hasRawInput()
    {
        return $this->rawInput !== null;
    }

    
    public function getRawInput()
    {
        return $this->rawInput;
    }

    
    public function setRawInput($value)
    {
        $this->rawInput = (string) $value;
        return $this;
    }

    
    public function hasCountryCodeSource()
    {
        return $this->countryCodeSource !== CountryCodeSource::UNSPECIFIED;
    }

    
    public function getCountryCodeSource()
    {
        return $this->countryCodeSource;
    }

    
    public function setCountryCodeSource($value)
    {
        $this->countryCodeSource = (int) $value;
        return $this;
    }

    
    public function hasPreferredDomesticCarrierCode()
    {
        return $this->preferredDomesticCarrierCode !== null;
    }

    
    public function getPreferredDomesticCarrierCode()
    {
        return $this->preferredDomesticCarrierCode;
    }

    
    public function setPreferredDomesticCarrierCode($value)
    {
        $this->preferredDomesticCarrierCode = (string) $value;
        return $this;
    }

    
    public function equals(PhoneNumber $other)
    {
        if ($this === $other) {
            return true;
        }

        return $this->countryCode === $other->countryCode
            && $this->nationalNumber === $other->nationalNumber
            && $this->extension === $other->extension
            && $this->italianLeadingZero === $other->italianLeadingZero
            && $this->numberOfLeadingZeros === $other->numberOfLeadingZeros
            && $this->rawInput === $other->rawInput
            && $this->countryCodeSource === $other->countryCodeSource
            && $this->preferredDomesticCarrierCode === $other->preferredDomesticCarrierCode;
    }

    
    public function __toString()
    {
        $outputString = '';

        $outputString .= 'Country Code: ' . $this->countryCode;
        $outputString .= ' National Number: ' . $this->nationalNumber;
        if ($this->hasItalianLeadingZero()) {
            $outputString .= ' Leading Zero(s): true';
        }
        if ($this->hasNumberOfLeadingZeros()) {
            $outputString .= ' Number of leading zeros: ' . $this->numberOfLeadingZeros;
        }
        if ($this->hasExtension()) {
            $outputString .= ' Extension: ' . $this->extension;
        }
        if ($this->hasCountryCodeSource()) {
            $outputString .= ' Country Code Source: ' . $this->countryCodeSource;
        }
        if ($this->hasPreferredDomesticCarrierCode()) {
            $outputString .= ' Preferred Domestic Carrier Code: ' . $this->preferredDomesticCarrierCode;
        }
        return $outputString;
    }

    
    public function serialize()
    {
        return serialize($this->__serialize());
    }

    public function __serialize()
    {
        return array(
            $this->countryCode,
            $this->nationalNumber,
            $this->extension,
            $this->italianLeadingZero,
            $this->numberOfLeadingZeros,
            $this->rawInput,
            $this->countryCodeSource,
            $this->preferredDomesticCarrierCode
        );
    }

    
    public function unserialize($serialized)
    {
        $this->__unserialize(unserialize($serialized));
    }

    public function __unserialize($data)
    {
        list(
            $this->countryCode,
            $this->nationalNumber,
            $this->extension,
            $this->italianLeadingZero,
            $this->numberOfLeadingZeros,
            $this->rawInput,
            $this->countryCodeSource,
            $this->preferredDomesticCarrierCode
        ) = $data;

        if ($this->numberOfLeadingZeros > 1) {
            $this->hasNumberOfLeadingZeros = true;
        }
    }
}

<?php

declare(strict_types=1);

namespace Brick\PhoneNumber;

use JsonSerializable;
use libphonenumber;
use libphonenumber\geocoding\PhoneNumberOfflineGeocoder;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;


final class PhoneNumber implements JsonSerializable
{
    
    private libphonenumber\PhoneNumber $phoneNumber;

    
    private function __construct(libphonenumber\PhoneNumber $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    
    public static function parse(string $phoneNumber, ?string $regionCode = null) : PhoneNumber
    {
        try {
            return new PhoneNumber(
                PhoneNumberUtil::getInstance()->parse($phoneNumber, $regionCode)
            );
        } catch (NumberParseException $e) {
            throw PhoneNumberParseException::wrap($e);
        }
    }

    
    public static function getExampleNumber(string $regionCode, int $phoneNumberType = PhoneNumberType::FIXED_LINE) : PhoneNumber
    {
        $phoneNumber = PhoneNumberUtil::getInstance()->getExampleNumberForType($regionCode, $phoneNumberType);

        if ($phoneNumber === null) {
            throw new PhoneNumberException('No example number is available for the given region and type.');
        }

        return new PhoneNumber($phoneNumber);
    }

    
    public function getCountryCode() : string
    {
        return (string) $this->phoneNumber->getCountryCode();
    }

    
    public function getGeographicalAreaCode() : string
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $nationalSignificantNumber = $phoneNumberUtil->getNationalSignificantNumber($this->phoneNumber);

        $areaCodeLength = $phoneNumberUtil->getLengthOfGeographicalAreaCode($this->phoneNumber);

        return substr($nationalSignificantNumber, 0, $areaCodeLength);
    }

    
    public function getNationalNumber() : string
    {
        return $this->phoneNumber->getNationalNumber();
    }

    
    public function getRegionCode() : ?string
    {
        $regionCode = PhoneNumberUtil::getInstance()->getRegionCodeForNumber($this->phoneNumber);

        if ($regionCode === '001') {
            return null;
        }

        return $regionCode;
    }

    
    public function isPossibleNumber() : bool
    {
        return PhoneNumberUtil::getInstance()->isPossibleNumber($this->phoneNumber);
    }

    
    public function isValidNumber() : bool
    {
        return PhoneNumberUtil::getInstance()->isValidNumber($this->phoneNumber);
    }

    
    public function getNumberType() : int
    {
        return PhoneNumberUtil::getInstance()->getNumberType($this->phoneNumber);
    }

    
    public function format(int $format) : string
    {
        return PhoneNumberUtil::getInstance()->format($this->phoneNumber, $format);
    }

    
    public function formatForCallingFrom(string $regionCode) : string
    {
        return PhoneNumberUtil::getInstance()->formatOutOfCountryCallingNumber($this->phoneNumber, $regionCode);
    }

    public function isEqualTo(PhoneNumber $phoneNumber): bool
    {
        return $this->phoneNumber->equals($phoneNumber->phoneNumber);
    }

    
    public function jsonSerialize(): string
    {
        return (string) $this;
    }

    
    public function getDescription(string $locale, ?string $userRegion = null) : ?string
    {
        $description = PhoneNumberOfflineGeocoder::getInstance()->getDescriptionForNumber(
            $this->phoneNumber,
            $locale,
            $userRegion
        );

        if ($description === '') {
            return null;
        }

        return $description;
    }

    
    public function __toString() : string
    {
        return $this->format(PhoneNumberFormat::E164);
    }
}

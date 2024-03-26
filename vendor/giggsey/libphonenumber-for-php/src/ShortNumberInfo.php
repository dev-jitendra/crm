<?php


namespace libphonenumber;

class ShortNumberInfo
{
    
    protected static $instance;
    
    protected $matcherAPI;
    protected $currentFilePrefix;
    protected $regionToMetadataMap = array();
    protected $countryCallingCodeToRegionCodeMap = array();
    protected $countryCodeToNonGeographicalMetadataMap = array();
    protected static $regionsWhereEmergencyNumbersMustBeExact = array(
        'BR',
        'CL',
        'NI',
    );

    protected function __construct(MatcherAPIInterface $matcherAPI)
    {
        $this->matcherAPI = $matcherAPI;

        
        $this->countryCallingCodeToRegionCodeMap = CountryCodeToRegionCodeMap::$countryCodeToRegionCodeMap;

        $this->currentFilePrefix = __DIR__ . '/data/ShortNumberMetadata';

        
        PhoneNumberUtil::getInstance();
    }

    
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new self(RegexBasedMatcher::create());
        }

        return static::$instance;
    }

    public static function resetInstance()
    {
        static::$instance = null;
    }

    
    protected function getRegionCodesForCountryCode($countryCallingCode)
    {
        if (!array_key_exists($countryCallingCode, $this->countryCallingCodeToRegionCodeMap)) {
            $regionCodes = null;
        } else {
            $regionCodes = $this->countryCallingCodeToRegionCodeMap[$countryCallingCode];
        }

        return ($regionCodes === null) ? array() : $regionCodes;
    }

    
    protected function regionDialingFromMatchesNumber(PhoneNumber $number, $regionDialingFrom)
    {
        if ($regionDialingFrom === null || $regionDialingFrom === '') {
            return false;
        }

        $regionCodes = $this->getRegionCodesForCountryCode($number->getCountryCode());

        return in_array(strtoupper($regionDialingFrom), $regionCodes);
    }

    public function getSupportedRegions()
    {
        return ShortNumbersRegionCodeSet::$shortNumbersRegionCodeSet;
    }

    
    public function getExampleShortNumber($regionCode)
    {
        $phoneMetadata = $this->getMetadataForRegion($regionCode);
        if ($phoneMetadata === null) {
            return '';
        }

        
        $desc = $phoneMetadata->getShortCode();
        if ($desc !== null && $desc->hasExampleNumber()) {
            return $desc->getExampleNumber();
        }
        return '';
    }

    
    public function getMetadataForRegion($regionCode)
    {
        $regionCode = strtoupper((string)$regionCode);

        if (!in_array($regionCode, ShortNumbersRegionCodeSet::$shortNumbersRegionCodeSet)) {
            return null;
        }

        if (!isset($this->regionToMetadataMap[$regionCode])) {
            
            
            $this->loadMetadataFromFile($this->currentFilePrefix, $regionCode, 0);
        }

        return isset($this->regionToMetadataMap[$regionCode]) ? $this->regionToMetadataMap[$regionCode] : null;
    }

    protected function loadMetadataFromFile($filePrefix, $regionCode, $countryCallingCode)
    {
        $isNonGeoRegion = PhoneNumberUtil::REGION_CODE_FOR_NON_GEO_ENTITY === $regionCode;
        $fileName = $filePrefix . '_' . ($isNonGeoRegion ? $countryCallingCode : $regionCode) . '.php';
        if (!is_readable($fileName)) {
            throw new \Exception('missing metadata: ' . $fileName);
        }

        $metadataLoader = new DefaultMetadataLoader();
        $data = $metadataLoader->loadMetadata($fileName);

        $metadata = new PhoneMetadata();
        $metadata->fromArray($data);
        if ($isNonGeoRegion) {
            $this->countryCodeToNonGeographicalMetadataMap[$countryCallingCode] = $metadata;
        } else {
            $this->regionToMetadataMap[$regionCode] = $metadata;
        }
    }

    
    public function getExampleShortNumberForCost($regionCode, $cost)
    {
        $phoneMetadata = $this->getMetadataForRegion($regionCode);
        if ($phoneMetadata === null) {
            return '';
        }

        
        $desc = null;
        switch ($cost) {
            case ShortNumberCost::TOLL_FREE:
                $desc = $phoneMetadata->getTollFree();
                break;
            case ShortNumberCost::STANDARD_RATE:
                $desc = $phoneMetadata->getStandardRate();
                break;
            case ShortNumberCost::PREMIUM_RATE:
                $desc = $phoneMetadata->getPremiumRate();
                break;
            default:
                
                break;
        }

        if ($desc !== null && $desc->hasExampleNumber()) {
            return $desc->getExampleNumber();
        }

        return '';
    }

    
    public function connectsToEmergencyNumber($number, $regionCode)
    {
        return $this->matchesEmergencyNumberHelper($number, $regionCode, true );
    }

    
    protected function matchesEmergencyNumberHelper($number, $regionCode, $allowPrefixMatch)
    {
        $number = PhoneNumberUtil::extractPossibleNumber($number);
        $matcher = new Matcher(PhoneNumberUtil::$PLUS_CHARS_PATTERN, $number);
        if ($matcher->lookingAt()) {
            
            
            
            return false;
        }

        $metadata = $this->getMetadataForRegion($regionCode);
        if ($metadata === null || !$metadata->hasEmergency()) {
            return false;
        }

        $normalizedNumber = PhoneNumberUtil::normalizeDigitsOnly($number);
        $emergencyDesc = $metadata->getEmergency();

        $allowPrefixMatchForRegion = (
            $allowPrefixMatch
            && !in_array(strtoupper($regionCode), static::$regionsWhereEmergencyNumbersMustBeExact)
        );

        return $this->matcherAPI->matchNationalNumber($normalizedNumber, $emergencyDesc, $allowPrefixMatchForRegion);
    }

    
    public function isCarrierSpecific(PhoneNumber $number)
    {
        $regionCodes = $this->getRegionCodesForCountryCode($number->getCountryCode());
        $regionCode = $this->getRegionCodeForShortNumberFromRegionList($number, $regionCodes);
        $nationalNumber = $this->getNationalSignificantNumber($number);
        $phoneMetadata = $this->getMetadataForRegion($regionCode);

        return ($phoneMetadata !== null) && $this->matchesPossibleNumberAndNationalNumber(
            $nationalNumber,
            $phoneMetadata->getCarrierSpecific()
        );
    }

    
    public function isCarrierSpecificForRegion(PhoneNumber $number, $regionDialingFrom)
    {
        if (!$this->regionDialingFromMatchesNumber($number, $regionDialingFrom)) {
            return false;
        }

        $nationalNumber = $this->getNationalSignificantNumber($number);
        $phoneMetadata = $this->getMetadataForRegion($regionDialingFrom);

        return ($phoneMetadata !== null)
            && $this->matchesPossibleNumberAndNationalNumber($nationalNumber, $phoneMetadata->getCarrierSpecific());
    }

    
    public function isSmsServiceForRegion(PhoneNumber $number, $regionDialingFrom)
    {
        if (!$this->regionDialingFromMatchesNumber($number, $regionDialingFrom)) {
            return false;
        }

        $phoneMetadata = $this->getMetadataForRegion($regionDialingFrom);

        return ($phoneMetadata !== null)
            && $this->matchesPossibleNumberAndNationalNumber(
                $this->getNationalSignificantNumber($number),
                $phoneMetadata->getSmsServices()
            );
    }

    
    protected function getRegionCodeForShortNumberFromRegionList(PhoneNumber $number, $regionCodes)
    {
        if (count($regionCodes) == 0) {
            return null;
        }

        if (count($regionCodes) == 1) {
            return $regionCodes[0];
        }

        $nationalNumber = $this->getNationalSignificantNumber($number);

        foreach ($regionCodes as $regionCode) {
            $phoneMetadata = $this->getMetadataForRegion($regionCode);
            if ($phoneMetadata !== null
                && $this->matchesPossibleNumberAndNationalNumber($nationalNumber, $phoneMetadata->getShortCode())
            ) {
                
                return $regionCode;
            }
        }
        return null;
    }

    
    public function isPossibleShortNumber(PhoneNumber $number)
    {
        $regionCodes = $this->getRegionCodesForCountryCode($number->getCountryCode());
        $shortNumberLength = strlen($this->getNationalSignificantNumber($number));

        foreach ($regionCodes as $region) {
            $phoneMetadata = $this->getMetadataForRegion($region);

            if ($phoneMetadata === null) {
                continue;
            }

            if (in_array($shortNumberLength, $phoneMetadata->getGeneralDesc()->getPossibleLength())) {
                return true;
            }
        }

        return false;
    }

    
    public function isPossibleShortNumberForRegion(PhoneNumber $shortNumber, $regionDialingFrom)
    {
        if (!$this->regionDialingFromMatchesNumber($shortNumber, $regionDialingFrom)) {
            return false;
        }

        $phoneMetadata = $this->getMetadataForRegion($regionDialingFrom);

        if ($phoneMetadata === null) {
            return false;
        }

        $numberLength = strlen($this->getNationalSignificantNumber($shortNumber));
        return in_array($numberLength, $phoneMetadata->getGeneralDesc()->getPossibleLength());
    }

    
    public function isValidShortNumber(PhoneNumber $number)
    {
        $regionCodes = $this->getRegionCodesForCountryCode($number->getCountryCode());
        $regionCode = $this->getRegionCodeForShortNumberFromRegionList($number, $regionCodes);
        if (count($regionCodes) > 1 && $regionCode !== null) {
            
            
            return true;
        }

        return $this->isValidShortNumberForRegion($number, $regionCode);
    }

    
    public function isValidShortNumberForRegion(PhoneNumber $number, $regionDialingFrom)
    {
        if (!$this->regionDialingFromMatchesNumber($number, $regionDialingFrom)) {
            return false;
        }
        $phoneMetadata = $this->getMetadataForRegion($regionDialingFrom);

        if ($phoneMetadata === null) {
            return false;
        }

        $shortNumber = $this->getNationalSignificantNumber($number);

        $generalDesc = $phoneMetadata->getGeneralDesc();

        if (!$this->matchesPossibleNumberAndNationalNumber($shortNumber, $generalDesc)) {
            return false;
        }

        $shortNumberDesc = $phoneMetadata->getShortCode();

        return $this->matchesPossibleNumberAndNationalNumber($shortNumber, $shortNumberDesc);
    }

    
    public function getExpectedCostForRegion(PhoneNumber $number, $regionDialingFrom)
    {
        if (!$this->regionDialingFromMatchesNumber($number, $regionDialingFrom)) {
            return ShortNumberCost::UNKNOWN_COST;
        }
        
        $phoneMetadata = $this->getMetadataForRegion($regionDialingFrom);
        if ($phoneMetadata === null) {
            return ShortNumberCost::UNKNOWN_COST;
        }

        $shortNumber = $this->getNationalSignificantNumber($number);

        
        
        
        if (!in_array(strlen($shortNumber), $phoneMetadata->getGeneralDesc()->getPossibleLength())) {
            return ShortNumberCost::UNKNOWN_COST;
        }

        
        
        if ($this->matchesPossibleNumberAndNationalNumber($shortNumber, $phoneMetadata->getPremiumRate())) {
            return ShortNumberCost::PREMIUM_RATE;
        }

        if ($this->matchesPossibleNumberAndNationalNumber($shortNumber, $phoneMetadata->getStandardRate())) {
            return ShortNumberCost::STANDARD_RATE;
        }

        if ($this->matchesPossibleNumberAndNationalNumber($shortNumber, $phoneMetadata->getTollFree())) {
            return ShortNumberCost::TOLL_FREE;
        }

        if ($this->isEmergencyNumber($shortNumber, $regionDialingFrom)) {
            
            return ShortNumberCost::TOLL_FREE;
        }

        return ShortNumberCost::UNKNOWN_COST;
    }

    
    public function getExpectedCost(PhoneNumber $number)
    {
        $regionCodes = $this->getRegionCodesForCountryCode($number->getCountryCode());
        if (count($regionCodes) == 0) {
            return ShortNumberCost::UNKNOWN_COST;
        }
        if (count($regionCodes) == 1) {
            return $this->getExpectedCostForRegion($number, $regionCodes[0]);
        }
        $cost = ShortNumberCost::TOLL_FREE;
        foreach ($regionCodes as $regionCode) {
            $costForRegion = $this->getExpectedCostForRegion($number, $regionCode);
            switch ($costForRegion) {
                case ShortNumberCost::PREMIUM_RATE:
                    return ShortNumberCost::PREMIUM_RATE;

                case ShortNumberCost::UNKNOWN_COST:
                    $cost = ShortNumberCost::UNKNOWN_COST;
                    break;

                case ShortNumberCost::STANDARD_RATE:
                    if ($cost != ShortNumberCost::UNKNOWN_COST) {
                        $cost = ShortNumberCost::STANDARD_RATE;
                    }
                    break;
                case ShortNumberCost::TOLL_FREE:
                    
                    break;
            }
        }
        return $cost;
    }

    
    public function isEmergencyNumber($number, $regionCode)
    {
        return $this->matchesEmergencyNumberHelper($number, $regionCode, false );
    }

    
    protected function getNationalSignificantNumber(PhoneNumber $number)
    {
        
        $nationalNumber = '';
        if ($number->isItalianLeadingZero()) {
            $zeros = str_repeat('0', $number->getNumberOfLeadingZeros());
            $nationalNumber .= $zeros;
        }

        $nationalNumber .= $number->getNationalNumber();

        return $nationalNumber;
    }

    
    protected function matchesPossibleNumberAndNationalNumber($number, PhoneNumberDesc $numberDesc)
    {
        if (count($numberDesc->getPossibleLength()) > 0 && !in_array(strlen($number), $numberDesc->getPossibleLength())) {
            return false;
        }

        return $this->matcherAPI->matchNationalNumber($number, $numberDesc, false);
    }
}

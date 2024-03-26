<?php

namespace libphonenumber;

use libphonenumber\Leniency\AbstractLeniency;


class PhoneNumberUtil
{
    
    const REGEX_FLAGS = 'ui'; 
    
    const MIN_LENGTH_FOR_NSN = 2;
    
    const MAX_LENGTH_FOR_NSN = 17;

    
    
    const MAX_INPUT_STRING_LENGTH = 250;

    
    const MAX_LENGTH_COUNTRY_CODE = 3;

    const REGION_CODE_FOR_NON_GEO_ENTITY = '001';

    
    const UNKNOWN_REGION = 'ZZ';

    const NANPA_COUNTRY_CODE = 1;
    
    const PLUS_SIGN = '+';
    const PLUS_CHARS = '+＋';
    const STAR_SIGN = '*';

    const RFC3966_EXTN_PREFIX = ';ext=';
    const RFC3966_PREFIX = 'tel:';
    const RFC3966_PHONE_CONTEXT = ';phone-context=';
    const RFC3966_ISDN_SUBADDRESS = ';isub=';

    
    
    const VALID_ALPHA_PHONE_PATTERN = '(?:.*?[A-Za-z]){3}.*';
    
    const VALID_ALPHA = 'A-Za-z';


    
    
    
    
    const DEFAULT_EXTN_PREFIX = ' ext. ';

    
    
    
    
    
    
    const VALID_PUNCTUATION = "-x\xE2\x80\x90-\xE2\x80\x95\xE2\x88\x92\xE3\x83\xBC\xEF\xBC\x8D-\xEF\xBC\x8F \xC2\xA0\xC2\xAD\xE2\x80\x8B\xE2\x81\xA0\xE3\x80\x80()\xEF\xBC\x88\xEF\xBC\x89\xEF\xBC\xBB\xEF\xBC\xBD.\\[\\]/~\xE2\x81\x93\xE2\x88\xBC";
    const DIGITS = "\\p{Nd}";

    
    
    
    
    
    
    const SINGLE_INTERNATIONAL_PREFIX = "[\\d]+(?:[~\xE2\x81\x93\xE2\x88\xBC\xEF\xBD\x9E][\\d]+)?";
    const NON_DIGITS_PATTERN = "(\\D+)";

    
    
    
    
    const FIRST_GROUP_PATTERN = "(\\$\\d)";
    
    
    const NP_STRING = '$NP';
    const FG_STRING = '$FG';
    const CC_STRING = '$CC';

    
    
    
    const FIRST_GROUP_ONLY_PREFIX_PATTERN = '\\(?\\$1\\)?';
    public static $PLUS_CHARS_PATTERN;
    protected static $SEPARATOR_PATTERN;
    protected static $CAPTURING_DIGIT_PATTERN;
    protected static $VALID_START_CHAR_PATTERN;
    public static $SECOND_NUMBER_START_PATTERN = '[\\\\/] *x';
    public static $UNWANTED_END_CHAR_PATTERN = "[[\\P{N}&&\\P{L}]&&[^#]]+$";
    protected static $DIALLABLE_CHAR_MAPPINGS = array();
    protected static $CAPTURING_EXTN_DIGITS;

    
    protected static $instance;

    
    protected static $ALPHA_MAPPINGS = array(
        'A' => '2',
        'B' => '2',
        'C' => '2',
        'D' => '3',
        'E' => '3',
        'F' => '3',
        'G' => '4',
        'H' => '4',
        'I' => '4',
        'J' => '5',
        'K' => '5',
        'L' => '5',
        'M' => '6',
        'N' => '6',
        'O' => '6',
        'P' => '7',
        'Q' => '7',
        'R' => '7',
        'S' => '7',
        'T' => '8',
        'U' => '8',
        'V' => '8',
        'W' => '9',
        'X' => '9',
        'Y' => '9',
        'Z' => '9',
    );

    
    protected static $MOBILE_TOKEN_MAPPINGS = array();

    
    protected static $GEO_MOBILE_COUNTRIES_WITHOUT_MOBILE_AREA_CODES;

    
    protected static $GEO_MOBILE_COUNTRIES;

    
    protected static $ALPHA_PHONE_MAPPINGS;

    
    protected static $ALL_PLUS_NUMBER_GROUPING_SYMBOLS;

    
    protected static $asciiDigitMappings = array(
        '0' => '0',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9',
    );

    
    protected static $EXTN_PATTERNS_FOR_PARSING;
    
    public static $EXTN_PATTERNS_FOR_MATCHING;

    
    
    protected static $RFC3966_VISUAL_SEPARATOR = "[\\-\\.\\(\\)]?";
    protected static $RFC3966_PHONE_DIGIT;
    protected static $RFC3966_GLOBAL_NUMBER_DIGITS;

    
    
    protected static $ALPHANUM;
    protected static $RFC3966_DOMAINLABEL;
    protected static $RFC3966_TOPLABEL;
    protected static $RFC3966_DOMAINNAME;

    protected static $EXTN_PATTERN;
    protected static $VALID_PHONE_NUMBER_PATTERN;
    protected static $MIN_LENGTH_PHONE_NUMBER_PATTERN;
    
    protected static $VALID_PHONE_NUMBER;
    protected static $numericCharacters = array(
        "\xef\xbc\x90" => 0,
        "\xef\xbc\x91" => 1,
        "\xef\xbc\x92" => 2,
        "\xef\xbc\x93" => 3,
        "\xef\xbc\x94" => 4,
        "\xef\xbc\x95" => 5,
        "\xef\xbc\x96" => 6,
        "\xef\xbc\x97" => 7,
        "\xef\xbc\x98" => 8,
        "\xef\xbc\x99" => 9,

        "\xd9\xa0" => 0,
        "\xd9\xa1" => 1,
        "\xd9\xa2" => 2,
        "\xd9\xa3" => 3,
        "\xd9\xa4" => 4,
        "\xd9\xa5" => 5,
        "\xd9\xa6" => 6,
        "\xd9\xa7" => 7,
        "\xd9\xa8" => 8,
        "\xd9\xa9" => 9,

        "\xdb\xb0" => 0,
        "\xdb\xb1" => 1,
        "\xdb\xb2" => 2,
        "\xdb\xb3" => 3,
        "\xdb\xb4" => 4,
        "\xdb\xb5" => 5,
        "\xdb\xb6" => 6,
        "\xdb\xb7" => 7,
        "\xdb\xb8" => 8,
        "\xdb\xb9" => 9,

        "\xe1\xa0\x90" => 0,
        "\xe1\xa0\x91" => 1,
        "\xe1\xa0\x92" => 2,
        "\xe1\xa0\x93" => 3,
        "\xe1\xa0\x94" => 4,
        "\xe1\xa0\x95" => 5,
        "\xe1\xa0\x96" => 6,
        "\xe1\xa0\x97" => 7,
        "\xe1\xa0\x98" => 8,
        "\xe1\xa0\x99" => 9,
    );

    
    protected $countryCodesForNonGeographicalRegion = array();
    
    protected $supportedRegions = array();

    
    protected $countryCallingCodeToRegionCodeMap = array();
    
    protected $nanpaRegions = array();

    
    protected $metadataSource;

    
    protected $matcherAPI;

    
    protected function __construct(MetadataSourceInterface $metadataSource, $countryCallingCodeToRegionCodeMap)
    {
        $this->metadataSource = $metadataSource;
        $this->countryCallingCodeToRegionCodeMap = $countryCallingCodeToRegionCodeMap;
        $this->init();
        $this->matcherAPI = RegexBasedMatcher::create();
        static::initExtnPatterns();
        static::initExtnPattern();
        static::initRFC3966Patterns();
        static::$PLUS_CHARS_PATTERN = '[' . static::PLUS_CHARS . ']+';
        static::$SEPARATOR_PATTERN = '[' . static::VALID_PUNCTUATION . ']+';
        static::$CAPTURING_DIGIT_PATTERN = '(' . static::DIGITS . ')';
        static::initValidStartCharPattern();
        static::initAlphaPhoneMappings();
        static::initDiallableCharMappings();

        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS = array();
        
        foreach (static::$ALPHA_MAPPINGS as $c => $value) {
            static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS[strtolower($c)] = $c;
            static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS[$c] = $c;
        }
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS += static::$asciiDigitMappings;
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS['-'] = '-';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xEF\xBC\x8D"] = '-';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xE2\x80\x90"] = '-';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xE2\x80\x91"] = '-';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xE2\x80\x92"] = '-';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xE2\x80\x93"] = '-';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xE2\x80\x94"] = '-';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xE2\x80\x95"] = '-';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xE2\x88\x92"] = '-';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS['/'] = '/';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xEF\xBC\x8F"] = '/';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS[' '] = ' ';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xE3\x80\x80"] = ' ';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xE2\x81\xA0"] = ' ';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS['.'] = '.';
        static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS["\xEF\xBC\x8E"] = '.';


        static::initValidPhoneNumberPatterns();

        static::$UNWANTED_END_CHAR_PATTERN = '[^' . static::DIGITS . static::VALID_ALPHA . '#]+$';

        static::initMobileTokenMappings();

        static::$GEO_MOBILE_COUNTRIES_WITHOUT_MOBILE_AREA_CODES = array();
        static::$GEO_MOBILE_COUNTRIES_WITHOUT_MOBILE_AREA_CODES[] = 86; 

        static::$GEO_MOBILE_COUNTRIES = array();
        static::$GEO_MOBILE_COUNTRIES[] = 52; 
        static::$GEO_MOBILE_COUNTRIES[] = 54; 
        static::$GEO_MOBILE_COUNTRIES[] = 55; 
        static::$GEO_MOBILE_COUNTRIES[] = 62; 

        static::$GEO_MOBILE_COUNTRIES = array_merge(static::$GEO_MOBILE_COUNTRIES, static::$GEO_MOBILE_COUNTRIES_WITHOUT_MOBILE_AREA_CODES);
    }

    
    public static function getInstance($baseFileLocation = null, array $countryCallingCodeToRegionCodeMap = null, MetadataLoaderInterface $metadataLoader = null, MetadataSourceInterface $metadataSource = null)
    {
        if (static::$instance === null) {
            if ($countryCallingCodeToRegionCodeMap === null) {
                $countryCallingCodeToRegionCodeMap = CountryCodeToRegionCodeMap::$countryCodeToRegionCodeMap;
            }

            if ($metadataLoader === null) {
                $metadataLoader = new DefaultMetadataLoader();
            }

            if ($metadataSource === null) {
                if ($baseFileLocation === null) {
                    $baseFileLocation = __DIR__ . '/data/PhoneNumberMetadata';
                }

                $metadataSource = new MultiFileMetadataSourceImpl($metadataLoader, $baseFileLocation);
            }

            static::$instance = new static($metadataSource, $countryCallingCodeToRegionCodeMap);
        }
        return static::$instance;
    }

    protected function init()
    {
        $supportedRegions = array(array());

        foreach ($this->countryCallingCodeToRegionCodeMap as $countryCode => $regionCodes) {
            
            
            if (count($regionCodes) === 1 && static::REGION_CODE_FOR_NON_GEO_ENTITY === $regionCodes[0]) {
                
                $this->countryCodesForNonGeographicalRegion[] = $countryCode;
            } else {
                
                $supportedRegions[] = $regionCodes;
            }
        }

        $this->supportedRegions = call_user_func_array('array_merge', $supportedRegions);


        
        
        
        $idx_region_code_non_geo_entity = array_search(static::REGION_CODE_FOR_NON_GEO_ENTITY, $this->supportedRegions);
        if ($idx_region_code_non_geo_entity !== false) {
            unset($this->supportedRegions[$idx_region_code_non_geo_entity]);
        }
        $this->nanpaRegions = $this->countryCallingCodeToRegionCodeMap[static::NANPA_COUNTRY_CODE];
    }

    
    public static function initExtnPatterns()
    {
        static::$EXTN_PATTERNS_FOR_PARSING = static::createExtnPattern(true);
        static::$EXTN_PATTERNS_FOR_MATCHING = static::createExtnPattern(false);
    }

    
    protected static function extnDigits($maxLength)
    {
        return '(' . self::DIGITS . '{1,' . $maxLength . '})';
    }

    
    protected static function createExtnPattern($forParsing)
    {
        
        
        
        
        $extLimitAfterExplicitLabel = 20;
        $extLimitAfterLikelyLabel = 15;
        $extLimitAfterAmbiguousChar = 9;
        $extLimitWhenNotSure = 6;



        $possibleSeparatorsBetweenNumberAndExtLabel = "[ \xC2\xA0\\t,]*";
        
        $possibleCharsAfterExtLabel = "[:\\.\xEf\xBC\x8E]?[ \xC2\xA0\\t,-]*";
        $optionalExtnSuffix = "#?";

        
        
        
        
        $explicitExtLabels = "(?:e?xt(?:ensi(?:o\xCC\x81?|\xC3\xB3))?n?|\xEF\xBD\x85?\xEF\xBD\x98\xEF\xBD\x94\xEF\xBD\x8E?|\xD0\xB4\xD0\xBE\xD0\xB1|anexo)";
        
        
        $ambiguousExtLabels = "(?:[x\xEF\xBD\x98#\xEF\xBC\x83~\xEF\xBD\x9E]|int|\xEF\xBD\x89\xEF\xBD\x8E\xEF\xBD\x94)";
        
        $ambiguousSeparator = "[- ]+";

        $rfcExtn = static::RFC3966_EXTN_PREFIX . static::extnDigits($extLimitAfterExplicitLabel);
        $explicitExtn = $possibleSeparatorsBetweenNumberAndExtLabel . $explicitExtLabels
            . $possibleCharsAfterExtLabel . static::extnDigits($extLimitAfterExplicitLabel)
            . $optionalExtnSuffix;
        $ambiguousExtn = $possibleSeparatorsBetweenNumberAndExtLabel . $ambiguousExtLabels
            . $possibleCharsAfterExtLabel . static::extnDigits($extLimitAfterAmbiguousChar) . $optionalExtnSuffix;
        $americanStyleExtnWithSuffix = $ambiguousSeparator . static::extnDigits($extLimitWhenNotSure) . "#";

        
        
        
        
        
        
        
        
        $extensionPattern =
            $rfcExtn . "|"
            . $explicitExtn . "|"
            . $ambiguousExtn . "|"
            . $americanStyleExtnWithSuffix;
        
        if ($forParsing) {
            
            
            $possibleSeparatorsNumberExtLabelNoComma = "[ \xC2\xA0\\t]*";
            
            
            
            $autoDiallingAndExtLabelsFound = "(?:,{2}|;)";

            $autoDiallingExtn = $possibleSeparatorsNumberExtLabelNoComma
                . $autoDiallingAndExtLabelsFound . $possibleCharsAfterExtLabel
                . static::extnDigits($extLimitAfterLikelyLabel) . $optionalExtnSuffix;
            $onlyCommasExtn = $possibleSeparatorsNumberExtLabelNoComma
                . '(?:,)+' . $possibleCharsAfterExtLabel . static::extnDigits($extLimitAfterAmbiguousChar)
                . $optionalExtnSuffix;
            
            
            
            
            return $extensionPattern . "|"
                . $autoDiallingExtn . "|"
                . $onlyCommasExtn;
        }
        return $extensionPattern;
    }

    protected static function initExtnPattern()
    {
        static::$EXTN_PATTERN = '/(?:' . static::$EXTN_PATTERNS_FOR_PARSING . ')$/' . static::REGEX_FLAGS;
    }

    protected static function initRFC3966Patterns()
    {
        static::$RFC3966_PHONE_DIGIT = '(' . static::DIGITS . '|' . static::$RFC3966_VISUAL_SEPARATOR . ')';
        static::$RFC3966_GLOBAL_NUMBER_DIGITS = "^\\" . static::PLUS_SIGN . static::$RFC3966_PHONE_DIGIT . "*" . static::DIGITS . static::$RFC3966_PHONE_DIGIT . "*$";

        static::$ALPHANUM = static::VALID_ALPHA . static::DIGITS;
        static::$RFC3966_DOMAINLABEL = '[' . static::$ALPHANUM . "]+((\\-)*[" . static::$ALPHANUM . "])*";
        static::$RFC3966_TOPLABEL = '[' . static::VALID_ALPHA . "]+((\\-)*[" . static::$ALPHANUM . "])*";
        static::$RFC3966_DOMAINNAME = "^(" . static::$RFC3966_DOMAINLABEL . "\\.)*" . static::$RFC3966_TOPLABEL . "\\.?$";
    }

    protected static function initValidPhoneNumberPatterns()
    {
        static::initExtnPatterns();
        static::$MIN_LENGTH_PHONE_NUMBER_PATTERN = '[' . static::DIGITS . ']{' . static::MIN_LENGTH_FOR_NSN . '}';
        static::$VALID_PHONE_NUMBER = '[' . static::PLUS_CHARS . ']*(?:[' . static::VALID_PUNCTUATION . static::STAR_SIGN . ']*[' . static::DIGITS . ']){3,}[' . static::VALID_PUNCTUATION . static::STAR_SIGN . static::VALID_ALPHA . static::DIGITS . ']*';
        static::$VALID_PHONE_NUMBER_PATTERN = '%^' . static::$MIN_LENGTH_PHONE_NUMBER_PATTERN . '$|^' . static::$VALID_PHONE_NUMBER . '(?:' . static::$EXTN_PATTERNS_FOR_PARSING . ')?$%' . static::REGEX_FLAGS;
    }

    protected static function initAlphaPhoneMappings()
    {
        static::$ALPHA_PHONE_MAPPINGS = static::$ALPHA_MAPPINGS + static::$asciiDigitMappings;
    }

    protected static function initValidStartCharPattern()
    {
        static::$VALID_START_CHAR_PATTERN = '[' . static::PLUS_CHARS . static::DIGITS . ']';
    }

    protected static function initMobileTokenMappings()
    {
        static::$MOBILE_TOKEN_MAPPINGS = array();
        static::$MOBILE_TOKEN_MAPPINGS['54'] = '9';
    }

    protected static function initDiallableCharMappings()
    {
        static::$DIALLABLE_CHAR_MAPPINGS = static::$asciiDigitMappings;
        static::$DIALLABLE_CHAR_MAPPINGS[static::PLUS_SIGN] = static::PLUS_SIGN;
        static::$DIALLABLE_CHAR_MAPPINGS['*'] = '*';
        static::$DIALLABLE_CHAR_MAPPINGS['#'] = '#';
    }

    
    public static function resetInstance()
    {
        static::$instance = null;
    }

    
    public static function convertAlphaCharactersInNumber($number)
    {
        if (static::$ALPHA_PHONE_MAPPINGS === null) {
            static::initAlphaPhoneMappings();
        }

        return static::normalizeHelper($number, static::$ALPHA_PHONE_MAPPINGS, false);
    }

    
    protected static function normalizeHelper($number, array $normalizationReplacements, $removeNonMatches)
    {
        $normalizedNumber = '';
        $strLength = mb_strlen($number, 'UTF-8');
        for ($i = 0; $i < $strLength; $i++) {
            $character = mb_substr($number, $i, 1, 'UTF-8');
            if (isset($normalizationReplacements[mb_strtoupper($character, 'UTF-8')])) {
                $normalizedNumber .= $normalizationReplacements[mb_strtoupper($character, 'UTF-8')];
            } elseif (!$removeNonMatches) {
                $normalizedNumber .= $character;
            }
            
        }
        return $normalizedNumber;
    }

    
    public static function formattingRuleHasFirstGroupOnly($nationalPrefixFormattingRule)
    {
        $firstGroupOnlyPrefixPatternMatcher = new Matcher(
            static::FIRST_GROUP_ONLY_PREFIX_PATTERN,
            $nationalPrefixFormattingRule
        );

        return $nationalPrefixFormattingRule === ''
            || $firstGroupOnlyPrefixPatternMatcher->matches();
    }

    
    public function getSupportedRegions()
    {
        return $this->supportedRegions;
    }

    
    public function getSupportedGlobalNetworkCallingCodes()
    {
        return $this->countryCodesForNonGeographicalRegion;
    }

    
    public function getSupportedCallingCodes()
    {
        return array_keys($this->countryCallingCodeToRegionCodeMap);
    }

    
    protected static function descHasPossibleNumberData(PhoneNumberDesc $desc)
    {
        
        
        $possibleLength = $desc->getPossibleLength();
        return count($possibleLength) != 1 || $possibleLength[0] != -1;
    }

    
    protected static function descHasData(PhoneNumberDesc $desc)
    {
        
        
        
        
        return $desc->hasExampleNumber()
            || static::descHasPossibleNumberData($desc)
            || $desc->hasNationalNumberPattern();
    }

    
    private function getSupportedTypesForMetadata(PhoneMetadata $metadata)
    {
        $types = array();
        foreach (array_keys(PhoneNumberType::values()) as $type) {
            if ($type === PhoneNumberType::FIXED_LINE_OR_MOBILE || $type === PhoneNumberType::UNKNOWN) {
                
                
                continue;
            }

            if (self::descHasData($this->getNumberDescByType($metadata, $type))) {
                $types[] = $type;
            }
        }

        return $types;
    }

    
    public function getSupportedTypesForRegion($regionCode)
    {
        if (!$this->isValidRegionCode($regionCode)) {
            return array();
        }
        $metadata = $this->getMetadataForRegion($regionCode);
        return $this->getSupportedTypesForMetadata($metadata);
    }

    
    public function getSupportedTypesForNonGeoEntity($countryCallingCode)
    {
        $metadata = $this->getMetadataForNonGeographicalRegion($countryCallingCode);
        if ($metadata === null) {
            return array();
        }

        return $this->getSupportedTypesForMetadata($metadata);
    }

    
    public function getLengthOfGeographicalAreaCode(PhoneNumber $number)
    {
        $metadata = $this->getMetadataForRegion($this->getRegionCodeForNumber($number));
        if ($metadata === null) {
            return 0;
        }
        
        
        if (!$metadata->hasNationalPrefix() && !$number->isItalianLeadingZero()) {
            return 0;
        }

        $type = $this->getNumberType($number);
        $countryCallingCode = $number->getCountryCode();

        if ($type === PhoneNumberType::MOBILE
            
            
            
            && in_array($countryCallingCode, self::$GEO_MOBILE_COUNTRIES_WITHOUT_MOBILE_AREA_CODES)
        ) {
            return 0;
        }

        if (!$this->isNumberGeographical($type, $countryCallingCode)) {
            return 0;
        }

        return $this->getLengthOfNationalDestinationCode($number);
    }

    
    public function getMetadataForRegion($regionCode)
    {
        if (!$this->isValidRegionCode($regionCode)) {
            return null;
        }

        return $this->metadataSource->getMetadataForRegion($regionCode);
    }

    
    protected function isValidRegionCode($regionCode)
    {
        return $regionCode !== null && !is_numeric($regionCode) && in_array(strtoupper($regionCode), $this->supportedRegions);
    }

    
    public function getRegionCodeForNumber(PhoneNumber $number)
    {
        $countryCode = $number->getCountryCode();
        if (!isset($this->countryCallingCodeToRegionCodeMap[$countryCode])) {
            return null;
        }
        $regions = $this->countryCallingCodeToRegionCodeMap[$countryCode];
        if (count($regions) == 1) {
            return $regions[0];
        }

        return $this->getRegionCodeForNumberFromRegionList($number, $regions);
    }

    
    protected function getRegionCodeForNumberFromRegionList(PhoneNumber $number, array $regionCodes)
    {
        $nationalNumber = $this->getNationalSignificantNumber($number);
        foreach ($regionCodes as $regionCode) {
            
            
            $metadata = $this->getMetadataForRegion($regionCode);
            if ($metadata->hasLeadingDigits()) {
                $nbMatches = preg_match(
                    '/' . $metadata->getLeadingDigits() . '/',
                    $nationalNumber,
                    $matches,
                    PREG_OFFSET_CAPTURE
                );
                if ($nbMatches > 0 && $matches[0][1] === 0) {
                    return $regionCode;
                }
            } elseif ($this->getNumberTypeHelper($nationalNumber, $metadata) != PhoneNumberType::UNKNOWN) {
                return $regionCode;
            }
        }
        return null;
    }

    
    public function getNationalSignificantNumber(PhoneNumber $number)
    {
        
        $nationalNumber = '';
        if ($number->isItalianLeadingZero() && $number->getNumberOfLeadingZeros() > 0) {
            $zeros = str_repeat('0', $number->getNumberOfLeadingZeros());
            $nationalNumber .= $zeros;
        }
        $nationalNumber .= $number->getNationalNumber();
        return $nationalNumber;
    }

    
    protected function getNumberTypeHelper($nationalNumber, PhoneMetadata $metadata)
    {
        if (!$this->isNumberMatchingDesc($nationalNumber, $metadata->getGeneralDesc())) {
            return PhoneNumberType::UNKNOWN;
        }
        if ($this->isNumberMatchingDesc($nationalNumber, $metadata->getPremiumRate())) {
            return PhoneNumberType::PREMIUM_RATE;
        }
        if ($this->isNumberMatchingDesc($nationalNumber, $metadata->getTollFree())) {
            return PhoneNumberType::TOLL_FREE;
        }


        if ($this->isNumberMatchingDesc($nationalNumber, $metadata->getSharedCost())) {
            return PhoneNumberType::SHARED_COST;
        }
        if ($this->isNumberMatchingDesc($nationalNumber, $metadata->getVoip())) {
            return PhoneNumberType::VOIP;
        }
        if ($this->isNumberMatchingDesc($nationalNumber, $metadata->getPersonalNumber())) {
            return PhoneNumberType::PERSONAL_NUMBER;
        }
        if ($this->isNumberMatchingDesc($nationalNumber, $metadata->getPager())) {
            return PhoneNumberType::PAGER;
        }
        if ($this->isNumberMatchingDesc($nationalNumber, $metadata->getUan())) {
            return PhoneNumberType::UAN;
        }
        if ($this->isNumberMatchingDesc($nationalNumber, $metadata->getVoicemail())) {
            return PhoneNumberType::VOICEMAIL;
        }
        $isFixedLine = $this->isNumberMatchingDesc($nationalNumber, $metadata->getFixedLine());
        if ($isFixedLine) {
            if ($metadata->getSameMobileAndFixedLinePattern()) {
                return PhoneNumberType::FIXED_LINE_OR_MOBILE;
            }

            if ($this->isNumberMatchingDesc($nationalNumber, $metadata->getMobile())) {
                return PhoneNumberType::FIXED_LINE_OR_MOBILE;
            }
            return PhoneNumberType::FIXED_LINE;
        }
        
        
        if (!$metadata->getSameMobileAndFixedLinePattern() &&
            $this->isNumberMatchingDesc($nationalNumber, $metadata->getMobile())
        ) {
            return PhoneNumberType::MOBILE;
        }
        return PhoneNumberType::UNKNOWN;
    }

    
    public function isNumberMatchingDesc($nationalNumber, PhoneNumberDesc $numberDesc)
    {
        
        
        
        $actualLength = mb_strlen($nationalNumber);
        $possibleLengths = $numberDesc->getPossibleLength();
        if (count($possibleLengths) > 0 && !in_array($actualLength, $possibleLengths)) {
            return false;
        }

        return $this->matcherAPI->matchNationalNumber($nationalNumber, $numberDesc, false);
    }

    
    public function isNumberGeographical($phoneNumberObjOrType, $countryCallingCode = null)
    {
        if ($phoneNumberObjOrType instanceof PhoneNumber) {
            return $this->isNumberGeographical($this->getNumberType($phoneNumberObjOrType), $phoneNumberObjOrType->getCountryCode());
        }

        return $phoneNumberObjOrType == PhoneNumberType::FIXED_LINE
        || $phoneNumberObjOrType == PhoneNumberType::FIXED_LINE_OR_MOBILE
        || (in_array($countryCallingCode, static::$GEO_MOBILE_COUNTRIES)
            && $phoneNumberObjOrType == PhoneNumberType::MOBILE);
    }

    
    public function getNumberType(PhoneNumber $number)
    {
        $regionCode = $this->getRegionCodeForNumber($number);
        $metadata = $this->getMetadataForRegionOrCallingCode($number->getCountryCode(), $regionCode);
        if ($metadata === null) {
            return PhoneNumberType::UNKNOWN;
        }
        $nationalSignificantNumber = $this->getNationalSignificantNumber($number);
        return $this->getNumberTypeHelper($nationalSignificantNumber, $metadata);
    }

    
    protected function getMetadataForRegionOrCallingCode($countryCallingCode, $regionCode)
    {
        return static::REGION_CODE_FOR_NON_GEO_ENTITY === $regionCode ?
            $this->getMetadataForNonGeographicalRegion($countryCallingCode) : $this->getMetadataForRegion($regionCode);
    }

    
    public function getMetadataForNonGeographicalRegion($countryCallingCode)
    {
        if (!isset($this->countryCallingCodeToRegionCodeMap[$countryCallingCode])) {
            return null;
        }
        return $this->metadataSource->getMetadataForNonGeographicalRegion($countryCallingCode);
    }

    
    public function getLengthOfNationalDestinationCode(PhoneNumber $number)
    {
        if ($number->hasExtension()) {
            
            
            $copiedProto = new PhoneNumber();
            $copiedProto->mergeFrom($number);
            $copiedProto->clearExtension();
        } else {
            $copiedProto = clone $number;
        }

        $nationalSignificantNumber = $this->format($copiedProto, PhoneNumberFormat::INTERNATIONAL);

        $numberGroups = preg_split('/' . static::NON_DIGITS_PATTERN . '/', $nationalSignificantNumber);

        
        
        
        if (count($numberGroups) <= 3) {
            return 0;
        }

        if ($this->getNumberType($number) == PhoneNumberType::MOBILE) {
            
            
            
            
            

            $mobileToken = static::getCountryMobileToken($number->getCountryCode());
            if ($mobileToken !== '') {
                return mb_strlen($numberGroups[2]) + mb_strlen($numberGroups[3]);
            }
        }
        return mb_strlen($numberGroups[2]);
    }

    
    public function format(PhoneNumber $number, $numberFormat)
    {
        if ($number->getNationalNumber() == 0 && $number->hasRawInput()) {
            
            
            
            
            
            $rawInput = $number->getRawInput();
            if ($rawInput !== '') {
                return $rawInput;
            }
        }

        $formattedNumber = '';
        $countryCallingCode = $number->getCountryCode();
        $nationalSignificantNumber = $this->getNationalSignificantNumber($number);

        if ($numberFormat == PhoneNumberFormat::E164) {
            
            
            $formattedNumber .= $nationalSignificantNumber;
            $this->prefixNumberWithCountryCallingCode($countryCallingCode, PhoneNumberFormat::E164, $formattedNumber);
            return $formattedNumber;
        }

        if (!$this->hasValidCountryCallingCode($countryCallingCode)) {
            $formattedNumber .= $nationalSignificantNumber;
            return $formattedNumber;
        }

        
        
        
        $regionCode = $this->getRegionCodeForCountryCode($countryCallingCode);
        
        
        $metadata = $this->getMetadataForRegionOrCallingCode($countryCallingCode, $regionCode);
        $formattedNumber .= $this->formatNsn($nationalSignificantNumber, $metadata, $numberFormat);
        $this->maybeAppendFormattedExtension($number, $metadata, $numberFormat, $formattedNumber);
        $this->prefixNumberWithCountryCallingCode($countryCallingCode, $numberFormat, $formattedNumber);
        return $formattedNumber;
    }

    
    protected function prefixNumberWithCountryCallingCode($countryCallingCode, $numberFormat, &$formattedNumber)
    {
        switch ($numberFormat) {
            case PhoneNumberFormat::E164:
                $formattedNumber = static::PLUS_SIGN . $countryCallingCode . $formattedNumber;
                return;
            case PhoneNumberFormat::INTERNATIONAL:
                $formattedNumber = static::PLUS_SIGN . $countryCallingCode . ' ' . $formattedNumber;
                return;
            case PhoneNumberFormat::RFC3966:
                $formattedNumber = static::RFC3966_PREFIX . static::PLUS_SIGN . $countryCallingCode . '-' . $formattedNumber;
                return;
            case PhoneNumberFormat::NATIONAL:
            default:
                return;
        }
    }

    
    protected function hasValidCountryCallingCode($countryCallingCode)
    {
        return isset($this->countryCallingCodeToRegionCodeMap[$countryCallingCode]);
    }

    
    public function getRegionCodeForCountryCode($countryCallingCode)
    {
        $regionCodes = isset($this->countryCallingCodeToRegionCodeMap[$countryCallingCode]) ? $this->countryCallingCodeToRegionCodeMap[$countryCallingCode] : null;
        return $regionCodes === null ? static::UNKNOWN_REGION : $regionCodes[0];
    }

    
    protected function formatNsn($number, PhoneMetadata $metadata, $numberFormat, $carrierCode = null)
    {
        $intlNumberFormats = $metadata->intlNumberFormats();
        
        
        $availableFormats = (count($intlNumberFormats) == 0 || $numberFormat == PhoneNumberFormat::NATIONAL)
            ? $metadata->numberFormats()
            : $metadata->intlNumberFormats();
        $formattingPattern = $this->chooseFormattingPatternForNumber($availableFormats, $number);
        return ($formattingPattern === null)
            ? $number
            : $this->formatNsnUsingPattern($number, $formattingPattern, $numberFormat, $carrierCode);
    }

    
    public function chooseFormattingPatternForNumber(array $availableFormats, $nationalNumber)
    {
        foreach ($availableFormats as $numFormat) {
            $leadingDigitsPatternMatcher = null;
            $size = $numFormat->leadingDigitsPatternSize();
            
            if ($size > 0) {
                $leadingDigitsPatternMatcher = new Matcher(
                    $numFormat->getLeadingDigitsPattern($size - 1),
                    $nationalNumber
                );
            }
            if ($size == 0 || $leadingDigitsPatternMatcher->lookingAt()) {
                $m = new Matcher($numFormat->getPattern(), $nationalNumber);
                if ($m->matches() > 0) {
                    return $numFormat;
                }
            }
        }
        return null;
    }

    
    public function formatNsnUsingPattern(
        $nationalNumber,
        NumberFormat $formattingPattern,
        $numberFormat,
        $carrierCode = null
    ) {
        $numberFormatRule = $formattingPattern->getFormat();
        $m = new Matcher($formattingPattern->getPattern(), $nationalNumber);
        if ($numberFormat === PhoneNumberFormat::NATIONAL &&
            $carrierCode !== null && $carrierCode !== '' &&
            $formattingPattern->getDomesticCarrierCodeFormattingRule() !== ''
        ) {
            
            $carrierCodeFormattingRule = $formattingPattern->getDomesticCarrierCodeFormattingRule();
            $carrierCodeFormattingRule = str_replace(static::CC_STRING, $carrierCode, $carrierCodeFormattingRule);
            
            
            $firstGroupMatcher = new Matcher(static::FIRST_GROUP_PATTERN, $numberFormatRule);
            $numberFormatRule = $firstGroupMatcher->replaceFirst($carrierCodeFormattingRule);
            $formattedNationalNumber = $m->replaceAll($numberFormatRule);
        } else {
            
            $nationalPrefixFormattingRule = $formattingPattern->getNationalPrefixFormattingRule();
            if ($numberFormat == PhoneNumberFormat::NATIONAL &&
                $nationalPrefixFormattingRule !== null &&
                mb_strlen($nationalPrefixFormattingRule) > 0
            ) {
                $firstGroupMatcher = new Matcher(static::FIRST_GROUP_PATTERN, $numberFormatRule);
                $formattedNationalNumber = $m->replaceAll(
                    $firstGroupMatcher->replaceFirst($nationalPrefixFormattingRule)
                );
            } else {
                $formattedNationalNumber = $m->replaceAll($numberFormatRule);
            }
        }
        if ($numberFormat == PhoneNumberFormat::RFC3966) {
            
            $matcher = new Matcher(static::$SEPARATOR_PATTERN, $formattedNationalNumber);
            if ($matcher->lookingAt()) {
                $formattedNationalNumber = $matcher->replaceFirst('');
            }
            
            $formattedNationalNumber = $matcher->reset($formattedNationalNumber)->replaceAll('-');
        }
        return $formattedNationalNumber;
    }

    
    protected function maybeAppendFormattedExtension(PhoneNumber $number, $metadata, $numberFormat, &$formattedNumber)
    {
        if ($number->hasExtension() && mb_strlen($number->getExtension()) > 0) {
            if ($numberFormat === PhoneNumberFormat::RFC3966) {
                $formattedNumber .= static::RFC3966_EXTN_PREFIX . $number->getExtension();
            } elseif (!empty($metadata) && $metadata->hasPreferredExtnPrefix()) {
                $formattedNumber .= $metadata->getPreferredExtnPrefix() . $number->getExtension();
            } else {
                $formattedNumber .= static::DEFAULT_EXTN_PREFIX . $number->getExtension();
            }
        }
    }

    
    public static function getCountryMobileToken($countryCallingCode)
    {
        if (count(static::$MOBILE_TOKEN_MAPPINGS) === 0) {
            static::initMobileTokenMappings();
        }

        if (array_key_exists($countryCallingCode, static::$MOBILE_TOKEN_MAPPINGS)) {
            return static::$MOBILE_TOKEN_MAPPINGS[$countryCallingCode];
        }
        return '';
    }

    
    public function isAlphaNumber($number)
    {
        if (!static::isViablePhoneNumber($number)) {
            
            return false;
        }
        $this->maybeStripExtension($number);
        return (bool)preg_match('/' . static::VALID_ALPHA_PHONE_PATTERN . '/' . static::REGEX_FLAGS, $number);
    }

    
    public static function isViablePhoneNumber($number)
    {
        if (static::$VALID_PHONE_NUMBER_PATTERN === null) {
            static::initValidPhoneNumberPatterns();
        }

        if (mb_strlen($number) < static::MIN_LENGTH_FOR_NSN) {
            return false;
        }

        $validPhoneNumberPattern = static::getValidPhoneNumberPattern();

        $m = preg_match($validPhoneNumberPattern, $number);
        return $m > 0;
    }

    
    protected static function getValidPhoneNumberPattern()
    {
        return static::$VALID_PHONE_NUMBER_PATTERN;
    }

    
    protected function maybeStripExtension(&$number)
    {
        $matches = array();
        $find = preg_match(static::$EXTN_PATTERN, $number, $matches, PREG_OFFSET_CAPTURE);
        
        
        if ($find > 0 && static::isViablePhoneNumber(substr($number, 0, $matches[0][1]))) {
            

            for ($i = 1, $length = count($matches); $i <= $length; $i++) {
                if ($matches[$i][0] != '') {
                    
                    
                    $extension = $matches[$i][0];
                    $number = substr($number, 0, $matches[0][1]);
                    return $extension;
                }
            }
        }
        return '';
    }

    
    public function parseAndKeepRawInput($numberToParse, $defaultRegion, PhoneNumber $phoneNumber = null)
    {
        if ($phoneNumber === null) {
            $phoneNumber = new PhoneNumber();
        }
        $this->parseHelper($numberToParse, $defaultRegion, true, true, $phoneNumber);
        return $phoneNumber;
    }

    
    public function findNumbers($text, $defaultRegion, AbstractLeniency $leniency = null, $maxTries = PHP_INT_MAX)
    {
        if ($leniency === null) {
            $leniency = Leniency::VALID();
        }

        return new PhoneNumberMatcher($this, $text, $defaultRegion, $leniency, $maxTries);
    }

    
    public function getAsYouTypeFormatter($regionCode)
    {
        return new AsYouTypeFormatter($regionCode);
    }

    
    public static function setItalianLeadingZerosForPhoneNumber($nationalNumber, PhoneNumber $phoneNumber)
    {
        if (strlen($nationalNumber) > 1 && substr($nationalNumber, 0, 1) == '0') {
            $phoneNumber->setItalianLeadingZero(true);
            $numberOfLeadingZeros = 1;
            
            
            while ($numberOfLeadingZeros < (strlen($nationalNumber) - 1) &&
                substr($nationalNumber, $numberOfLeadingZeros, 1) == '0') {
                $numberOfLeadingZeros++;
            }

            if ($numberOfLeadingZeros != 1) {
                $phoneNumber->setNumberOfLeadingZeros($numberOfLeadingZeros);
            }
        }
    }

    
    protected function parseHelper($numberToParse, $defaultRegion, $keepRawInput, $checkRegion, PhoneNumber $phoneNumber)
    {
        if ($numberToParse === null) {
            throw new NumberParseException(NumberParseException::NOT_A_NUMBER, 'The phone number supplied was null.');
        }

        $numberToParse = trim($numberToParse);

        if (mb_strlen($numberToParse) > static::MAX_INPUT_STRING_LENGTH) {
            throw new NumberParseException(
                NumberParseException::TOO_LONG,
                'The string supplied was too long to parse.'
            );
        }

        $nationalNumber = '';
        $this->buildNationalNumberForParsing($numberToParse, $nationalNumber);

        if (!static::isViablePhoneNumber($nationalNumber)) {
            throw new NumberParseException(
                NumberParseException::NOT_A_NUMBER,
                'The string supplied did not seem to be a phone number.'
            );
        }

        
        
        if ($checkRegion && !$this->checkRegionForParsing($nationalNumber, $defaultRegion)) {
            throw new NumberParseException(
                NumberParseException::INVALID_COUNTRY_CODE,
                'Missing or invalid default region.'
            );
        }

        if ($keepRawInput) {
            $phoneNumber->setRawInput($numberToParse);
        }
        
        
        $extension = $this->maybeStripExtension($nationalNumber);
        if ($extension !== '') {
            $phoneNumber->setExtension($extension);
        }

        $regionMetadata = $this->getMetadataForRegion($defaultRegion);
        
        
        $normalizedNationalNumber = '';
        try {
            
            
            
            $countryCode = $this->maybeExtractCountryCode(
                $nationalNumber,
                $regionMetadata,
                $normalizedNationalNumber,
                $keepRawInput,
                $phoneNumber
            );
        } catch (NumberParseException $e) {
            $matcher = new Matcher(static::$PLUS_CHARS_PATTERN, $nationalNumber);
            if ($e->getErrorType() == NumberParseException::INVALID_COUNTRY_CODE && $matcher->lookingAt()) {
                
                $countryCode = $this->maybeExtractCountryCode(
                    substr($nationalNumber, $matcher->end()),
                    $regionMetadata,
                    $normalizedNationalNumber,
                    $keepRawInput,
                    $phoneNumber
                );
                if ($countryCode == 0) {
                    throw new NumberParseException(
                        NumberParseException::INVALID_COUNTRY_CODE,
                        'Could not interpret numbers after plus-sign.'
                    );
                }
            } else {
                throw new NumberParseException($e->getErrorType(), $e->getMessage(), $e);
            }
        }
        if ($countryCode !== 0) {
            $phoneNumberRegion = $this->getRegionCodeForCountryCode($countryCode);
            if ($phoneNumberRegion != $defaultRegion) {
                
                $regionMetadata = $this->getMetadataForRegionOrCallingCode($countryCode, $phoneNumberRegion);
            }
        } else {
            
            

            $normalizedNationalNumber .= static::normalize($nationalNumber);
            if ($defaultRegion !== null) {
                $countryCode = $regionMetadata->getCountryCode();
                $phoneNumber->setCountryCode($countryCode);
            } elseif ($keepRawInput) {
                $phoneNumber->clearCountryCodeSource();
            }
        }
        if (mb_strlen($normalizedNationalNumber) < static::MIN_LENGTH_FOR_NSN) {
            throw new NumberParseException(
                NumberParseException::TOO_SHORT_NSN,
                'The string supplied is too short to be a phone number.'
            );
        }
        if ($regionMetadata !== null) {
            $carrierCode = '';
            $potentialNationalNumber = $normalizedNationalNumber;
            $this->maybeStripNationalPrefixAndCarrierCode($potentialNationalNumber, $regionMetadata, $carrierCode);
            
            
            
            $validationResult = $this->testNumberLength($potentialNationalNumber, $regionMetadata);
            if ($validationResult !== ValidationResult::TOO_SHORT
                && $validationResult !== ValidationResult::IS_POSSIBLE_LOCAL_ONLY
                && $validationResult !== ValidationResult::INVALID_LENGTH) {
                $normalizedNationalNumber = $potentialNationalNumber;
                if ($keepRawInput && $carrierCode !== '') {
                    $phoneNumber->setPreferredDomesticCarrierCode($carrierCode);
                }
            }
        }
        $lengthOfNationalNumber = mb_strlen($normalizedNationalNumber);
        if ($lengthOfNationalNumber < static::MIN_LENGTH_FOR_NSN) {
            throw new NumberParseException(
                NumberParseException::TOO_SHORT_NSN,
                'The string supplied is too short to be a phone number.'
            );
        }
        if ($lengthOfNationalNumber > static::MAX_LENGTH_FOR_NSN) {
            throw new NumberParseException(
                NumberParseException::TOO_LONG,
                'The string supplied is too long to be a phone number.'
            );
        }
        static::setItalianLeadingZerosForPhoneNumber($normalizedNationalNumber, $phoneNumber);

        
        if ((int)$normalizedNationalNumber == 0) {
            $normalizedNationalNumber = '0';
        } else {
            $normalizedNationalNumber = ltrim($normalizedNationalNumber, '0');
        }

        $phoneNumber->setNationalNumber($normalizedNationalNumber);
    }

    
    protected function extractPhoneContext($numberToExtractFrom, $indexOfPhoneContext)
    {
        
        if ($indexOfPhoneContext === false) {
            return null;
        }

        $phoneContextStart = $indexOfPhoneContext + strlen(static::RFC3966_PHONE_CONTEXT);
        
        if ($phoneContextStart >= mb_strlen($numberToExtractFrom)) {
            return '';
        }

        $phoneContextEnd = strpos($numberToExtractFrom, ';', $phoneContextStart);
        
        if ($phoneContextEnd !== false) {
            return substr($numberToExtractFrom, $phoneContextStart, $phoneContextEnd - $phoneContextStart);
        }

        return substr($numberToExtractFrom, $phoneContextStart);
    }

    
    protected function isPhoneContextValid($phoneContext)
    {
        if ($phoneContext === null) {
            return true;
        }

        if ($phoneContext === '') {
            return false;
        }

        $numberDigitsPattern = '/' . static::$RFC3966_GLOBAL_NUMBER_DIGITS . '/' . static::REGEX_FLAGS;
        $domainNamePattern = '/' . static::$RFC3966_DOMAINNAME . '/' . static::REGEX_FLAGS;

        
        return preg_match($numberDigitsPattern, $phoneContext) || preg_match($domainNamePattern, $phoneContext);
    }

    
    protected static function copyCoreFieldsOnly(PhoneNumber $phoneNumberIn)
    {
        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode($phoneNumberIn->getCountryCode());
        $phoneNumber->setNationalNumber($phoneNumberIn->getNationalNumber());
        if ($phoneNumberIn->getExtension() != '') {
            $phoneNumber->setExtension($phoneNumberIn->getExtension());
        }
        if ($phoneNumberIn->isItalianLeadingZero()) {
            $phoneNumber->setItalianLeadingZero(true);
            
            $phoneNumber->setNumberOfLeadingZeros($phoneNumberIn->getNumberOfLeadingZeros());
        }
        return $phoneNumber;
    }

    
    protected function buildNationalNumberForParsing($numberToParse, &$nationalNumber)
    {
        $indexOfPhoneContext = strpos($numberToParse, static::RFC3966_PHONE_CONTEXT);
        $phoneContext = $this->extractPhoneContext($numberToParse, $indexOfPhoneContext);

        if (!$this->isPhoneContextValid($phoneContext)) {
            throw new NumberParseException(NumberParseException::NOT_A_NUMBER, 'The phone-context valid is invalid.');
        }

        if ($phoneContext !== null) {
            
            

            if (strpos($phoneContext, self::PLUS_SIGN) === 0) {
                
                
                
                $nationalNumber .= $phoneContext;
            }

            
            
            
            
            $indexOfRfc3966Prefix = strpos($numberToParse, static::RFC3966_PREFIX);
            $indexOfNationalNumber = ($indexOfRfc3966Prefix !== false) ? $indexOfRfc3966Prefix + strlen(static::RFC3966_PREFIX) : 0;
            $nationalNumber .= substr(
                $numberToParse,
                $indexOfNationalNumber,
                $indexOfPhoneContext - $indexOfNationalNumber
            );
        } else {
            
            
            $nationalNumber .= static::extractPossibleNumber($numberToParse);
        }

        
        
        $indexOfIsdn = strpos($nationalNumber, static::RFC3966_ISDN_SUBADDRESS);
        if ($indexOfIsdn > 0) {
            $nationalNumber = substr($nationalNumber, 0, $indexOfIsdn);
        }
        
        
        
        
    }

    
    public static function extractPossibleNumber($number)
    {
        if (static::$VALID_START_CHAR_PATTERN === null) {
            static::initValidStartCharPattern();
        }

        $matches = array();
        $match = preg_match('/' . static::$VALID_START_CHAR_PATTERN . '/ui', $number, $matches, PREG_OFFSET_CAPTURE);
        if ($match > 0) {
            $number = substr($number, $matches[0][1]);
            
            $trailingCharsMatcher = new Matcher(static::$UNWANTED_END_CHAR_PATTERN, $number);
            if ($trailingCharsMatcher->find() && $trailingCharsMatcher->start() > 0) {
                $number = substr($number, 0, $trailingCharsMatcher->start());
            }

            
            $match = preg_match('%' . static::$SECOND_NUMBER_START_PATTERN . '%', $number, $matches, PREG_OFFSET_CAPTURE);
            if ($match > 0) {
                $number = substr($number, 0, $matches[0][1]);
            }

            return $number;
        }

        return '';
    }

    
    protected function checkRegionForParsing($numberToParse, $defaultRegion)
    {
        if (!$this->isValidRegionCode($defaultRegion)) {
            
            $plusCharsPatternMatcher = new Matcher(static::$PLUS_CHARS_PATTERN, $numberToParse);
            if ($numberToParse === null || $numberToParse === '' || !$plusCharsPatternMatcher->lookingAt()) {
                return false;
            }
        }
        return true;
    }

    
    public function maybeExtractCountryCode(
        $number,
        PhoneMetadata $defaultRegionMetadata = null,
        &$nationalNumber,
        $keepRawInput,
        PhoneNumber $phoneNumber
    ) {
        if ($number === '') {
            return 0;
        }
        $fullNumber = $number;
        
        $possibleCountryIddPrefix = 'NonMatch';
        if ($defaultRegionMetadata !== null) {
            $possibleCountryIddPrefix = $defaultRegionMetadata->getInternationalPrefix();
        }
        $countryCodeSource = $this->maybeStripInternationalPrefixAndNormalize($fullNumber, $possibleCountryIddPrefix);

        if ($keepRawInput) {
            $phoneNumber->setCountryCodeSource($countryCodeSource);
        }
        if ($countryCodeSource != CountryCodeSource::FROM_DEFAULT_COUNTRY) {
            if (mb_strlen($fullNumber) <= static::MIN_LENGTH_FOR_NSN) {
                throw new NumberParseException(
                    NumberParseException::TOO_SHORT_AFTER_IDD,
                    'Phone number had an IDD, but after this was not long enough to be a viable phone number.'
                );
            }
            $potentialCountryCode = $this->extractCountryCode($fullNumber, $nationalNumber);

            if ($potentialCountryCode != 0) {
                $phoneNumber->setCountryCode($potentialCountryCode);
                return $potentialCountryCode;
            }

            
            
            throw new NumberParseException(
                NumberParseException::INVALID_COUNTRY_CODE,
                'Country calling code supplied was not recognised.'
            );
        }

        if ($defaultRegionMetadata !== null) {
            
            
            
            $defaultCountryCode = $defaultRegionMetadata->getCountryCode();
            $defaultCountryCodeString = (string)$defaultCountryCode;
            $normalizedNumber = $fullNumber;
            if (strpos($normalizedNumber, $defaultCountryCodeString) === 0) {
                $potentialNationalNumber = substr($normalizedNumber, mb_strlen($defaultCountryCodeString));
                $generalDesc = $defaultRegionMetadata->getGeneralDesc();
                
                $carriercode = null;
                $this->maybeStripNationalPrefixAndCarrierCode(
                    $potentialNationalNumber,
                    $defaultRegionMetadata,
                    $carriercode
                );
                
                
                
                if ((!$this->matcherAPI->matchNationalNumber($fullNumber, $generalDesc, false)
                        && $this->matcherAPI->matchNationalNumber($potentialNationalNumber, $generalDesc, false))
                    || $this->testNumberLength($fullNumber, $defaultRegionMetadata) === ValidationResult::TOO_LONG
                ) {
                    $nationalNumber .= $potentialNationalNumber;
                    if ($keepRawInput) {
                        $phoneNumber->setCountryCodeSource(CountryCodeSource::FROM_NUMBER_WITHOUT_PLUS_SIGN);
                    }
                    $phoneNumber->setCountryCode($defaultCountryCode);
                    return $defaultCountryCode;
                }
            }
        }
        
        $phoneNumber->setCountryCode(0);
        return 0;
    }

    
    public function maybeStripInternationalPrefixAndNormalize(&$number, $possibleIddPrefix)
    {
        if ($number === '') {
            return CountryCodeSource::FROM_DEFAULT_COUNTRY;
        }
        $matches = array();
        
        $match = preg_match('/^' . static::$PLUS_CHARS_PATTERN . '/' . static::REGEX_FLAGS, $number, $matches, PREG_OFFSET_CAPTURE);
        if ($match > 0) {
            $number = mb_substr($number, $matches[0][1] + mb_strlen($matches[0][0]));
            
            $number = static::normalize($number);
            return CountryCodeSource::FROM_NUMBER_WITH_PLUS_SIGN;
        }
        
        $iddPattern = $possibleIddPrefix;
        $number = static::normalize($number);
        return $this->parsePrefixAsIdd($iddPattern, $number)
            ? CountryCodeSource::FROM_NUMBER_WITH_IDD
            : CountryCodeSource::FROM_DEFAULT_COUNTRY;
    }

    
    public static function normalize(&$number)
    {
        if (static::$ALPHA_PHONE_MAPPINGS === null) {
            static::initAlphaPhoneMappings();
        }

        $m = new Matcher(static::VALID_ALPHA_PHONE_PATTERN, $number);
        if ($m->matches()) {
            return static::normalizeHelper($number, static::$ALPHA_PHONE_MAPPINGS, true);
        }

        return static::normalizeDigitsOnly($number);
    }

    
    public static function normalizeDigitsOnly($number)
    {
        return static::normalizeDigits($number, false );
    }

    
    public static function normalizeDigits($number, $keepNonDigits)
    {
        $normalizedDigits = '';
        $numberAsArray = preg_split('/(?<!^)(?!$)/u', $number);
        foreach ($numberAsArray as $character) {
            
            if (array_key_exists($character, static::$numericCharacters)) {
                $normalizedDigits .= static::$numericCharacters[$character];
            } elseif (is_numeric($character)) {
                $normalizedDigits .= $character;
            } elseif ($keepNonDigits) {
                $normalizedDigits .= $character;
            }
        }
        return $normalizedDigits;
    }

    
    protected function parsePrefixAsIdd($iddPattern, &$number)
    {
        $m = new Matcher($iddPattern, $number);
        if ($m->lookingAt()) {
            $matchEnd = $m->end();
            
            
            $digitMatcher = new Matcher(static::$CAPTURING_DIGIT_PATTERN, substr($number, $matchEnd));
            if ($digitMatcher->find()) {
                $normalizedGroup = static::normalizeDigitsOnly($digitMatcher->group(1));
                if ($normalizedGroup == '0') {
                    return false;
                }
            }
            $number = substr($number, $matchEnd);
            return true;
        }
        return false;
    }

    
    public function extractCountryCode($fullNumber, &$nationalNumber)
    {
        if (($fullNumber === '') || ($fullNumber[0] == '0')) {
            
            return 0;
        }
        $numberLength = mb_strlen($fullNumber);
        for ($i = 1; $i <= static::MAX_LENGTH_COUNTRY_CODE && $i <= $numberLength; $i++) {
            $potentialCountryCode = (int)substr($fullNumber, 0, $i);
            if (isset($this->countryCallingCodeToRegionCodeMap[$potentialCountryCode])) {
                $nationalNumber .= substr($fullNumber, $i);
                return $potentialCountryCode;
            }
        }
        return 0;
    }

    
    public function maybeStripNationalPrefixAndCarrierCode(&$number, PhoneMetadata $metadata, &$carrierCode)
    {
        $numberLength = mb_strlen($number);
        $possibleNationalPrefix = $metadata->getNationalPrefixForParsing();
        if ($numberLength == 0 || $possibleNationalPrefix === null || $possibleNationalPrefix === '') {
            
            return false;
        }

        
        $prefixMatcher = new Matcher($possibleNationalPrefix, $number);
        if ($prefixMatcher->lookingAt()) {
            $generalDesc = $metadata->getGeneralDesc();
            
            $isViableOriginalNumber = $this->matcherAPI->matchNationalNumber($number, $generalDesc, false);
            
            
            
            $numOfGroups = $prefixMatcher->groupCount();
            $transformRule = $metadata->getNationalPrefixTransformRule();
            if ($transformRule === null
                || $transformRule === ''
                || $prefixMatcher->group($numOfGroups - 1) === null
            ) {
                
                if ($isViableOriginalNumber &&
                    !$this->matcherAPI->matchNationalNumber(
                        substr($number, $prefixMatcher->end()),
                        $generalDesc,
                        false
                    )) {
                    return false;
                }
                if ($carrierCode !== null && $numOfGroups > 0 && $prefixMatcher->group($numOfGroups) !== null) {
                    $carrierCode .= $prefixMatcher->group(1);
                }

                $number = substr($number, $prefixMatcher->end());
                return true;
            }

            
            
            $transformedNumber = $number;
            $transformedNumber = substr_replace(
                $transformedNumber,
                $prefixMatcher->replaceFirst($transformRule),
                0,
                $numberLength
            );
            if ($isViableOriginalNumber
                && !$this->matcherAPI->matchNationalNumber($transformedNumber, $generalDesc, false)) {
                return false;
            }
            if ($carrierCode !== null && $numOfGroups > 1) {
                $carrierCode .= $prefixMatcher->group(1);
            }
            $number = substr_replace($number, $transformedNumber, 0, mb_strlen($number));
            return true;
        }
        return false;
    }

    
    public function isPossibleNumberForType(PhoneNumber $number, $type)
    {
        $result = $this->isPossibleNumberForTypeWithReason($number, $type);
        return $result === ValidationResult::IS_POSSIBLE
            || $result === ValidationResult::IS_POSSIBLE_LOCAL_ONLY;
    }

    
    protected function testNumberLength($number, PhoneMetadata $metadata, $type = PhoneNumberType::UNKNOWN)
    {
        $descForType = $this->getNumberDescByType($metadata, $type);
        
        
        
        
        
        
        $possibleLengths = (count($descForType->getPossibleLength()) === 0)
            ? $metadata->getGeneralDesc()->getPossibleLength() : $descForType->getPossibleLength();

        $localLengths = $descForType->getPossibleLengthLocalOnly();

        if ($type === PhoneNumberType::FIXED_LINE_OR_MOBILE) {
            if (!static::descHasPossibleNumberData($this->getNumberDescByType($metadata, PhoneNumberType::FIXED_LINE))) {
                
                
                return $this->testNumberLength($number, $metadata, PhoneNumberType::MOBILE);
            }

            $mobileDesc = $this->getNumberDescByType($metadata, PhoneNumberType::MOBILE);
            if (static::descHasPossibleNumberData($mobileDesc)) {
                
                
                
                $possibleLengths = array_merge(
                    $possibleLengths,
                    (count($mobileDesc->getPossibleLength()) === 0)
                        ? $metadata->getGeneralDesc()->getPossibleLength() : $mobileDesc->getPossibleLength()
                );

                
                
                sort($possibleLengths);

                if (count($localLengths) === 0) {
                    $localLengths = $mobileDesc->getPossibleLengthLocalOnly();
                } else {
                    $localLengths = array_merge($localLengths, $mobileDesc->getPossibleLengthLocalOnly());
                    sort($localLengths);
                }
            }
        }


        
        

        if ($possibleLengths[0] === -1) {
            return ValidationResult::INVALID_LENGTH;
        }

        $actualLength = mb_strlen($number);

        
        

        if (in_array($actualLength, $localLengths)) {
            return ValidationResult::IS_POSSIBLE_LOCAL_ONLY;
        }

        $minimumLength = reset($possibleLengths);
        if ($minimumLength == $actualLength) {
            return ValidationResult::IS_POSSIBLE;
        }

        if ($minimumLength > $actualLength) {
            return ValidationResult::TOO_SHORT;
        } elseif (isset($possibleLengths[count($possibleLengths) - 1]) && $possibleLengths[count($possibleLengths) - 1] < $actualLength) {
            return ValidationResult::TOO_LONG;
        }

        
        array_shift($possibleLengths);
        return in_array($actualLength, $possibleLengths) ? ValidationResult::IS_POSSIBLE : ValidationResult::INVALID_LENGTH;
    }

    
    public function getRegionCodesForCountryCode($countryCallingCode)
    {
        $regionCodes = isset($this->countryCallingCodeToRegionCodeMap[$countryCallingCode]) ? $this->countryCallingCodeToRegionCodeMap[$countryCallingCode] : null;
        return $regionCodes === null ? array() : $regionCodes;
    }

    
    public function getCountryCodeForRegion($regionCode)
    {
        if (!$this->isValidRegionCode($regionCode)) {
            return 0;
        }
        return $this->getCountryCodeForValidRegion($regionCode);
    }

    
    protected function getCountryCodeForValidRegion($regionCode)
    {
        $metadata = $this->getMetadataForRegion($regionCode);
        if ($metadata === null) {
            throw new \InvalidArgumentException('Invalid region code: ' . $regionCode);
        }
        return $metadata->getCountryCode();
    }

    
    public function formatNumberForMobileDialing(PhoneNumber $number, $regionCallingFrom, $withFormatting)
    {
        $countryCallingCode = $number->getCountryCode();
        if (!$this->hasValidCountryCallingCode($countryCallingCode)) {
            return $number->hasRawInput() ? $number->getRawInput() : '';
        }

        $formattedNumber = '';
        
        $numberNoExt = new PhoneNumber();
        $numberNoExt->mergeFrom($number)->clearExtension();
        $regionCode = $this->getRegionCodeForCountryCode($countryCallingCode);
        $numberType = $this->getNumberType($numberNoExt);
        $isValidNumber = ($numberType !== PhoneNumberType::UNKNOWN);
        if (strtoupper($regionCallingFrom) === $regionCode) {
            $isFixedLineOrMobile = ($numberType == PhoneNumberType::FIXED_LINE || $numberType == PhoneNumberType::MOBILE || $numberType == PhoneNumberType::FIXED_LINE_OR_MOBILE);
            
            if ($regionCode === 'BR' && $isFixedLineOrMobile) {
                
                
                
                $formattedNumber = $numberNoExt->getPreferredDomesticCarrierCode() !== ''
                    ? $this->formatNationalNumberWithPreferredCarrierCode($numberNoExt, '')
                    
                    
                    
                    : '';
            } elseif ($countryCallingCode === static::NANPA_COUNTRY_CODE) {
                
                
                
                $regionMetadata = $this->getMetadataForRegion($regionCallingFrom);
                if ($this->canBeInternationallyDialled($numberNoExt)
                    && $this->testNumberLength($this->getNationalSignificantNumber($numberNoExt), $regionMetadata)
                    !== ValidationResult::TOO_SHORT
                ) {
                    $formattedNumber = $this->format($numberNoExt, PhoneNumberFormat::INTERNATIONAL);
                } else {
                    $formattedNumber = $this->format($numberNoExt, PhoneNumberFormat::NATIONAL);
                }
            } elseif ((
                $regionCode == static::REGION_CODE_FOR_NON_GEO_ENTITY ||
                    
                    
                    
                    
                    
                    
                    
                    
                    (
                        ($regionCode === 'MX' || $regionCode === 'CL' || $regionCode === 'UZ')
                        && $isFixedLineOrMobile
                    )
            ) && $this->canBeInternationallyDialled($numberNoExt)
            ) {
                $formattedNumber = $this->format($numberNoExt, PhoneNumberFormat::INTERNATIONAL);
            } else {
                $formattedNumber = $this->format($numberNoExt, PhoneNumberFormat::NATIONAL);
            }
        } elseif ($isValidNumber && $this->canBeInternationallyDialled($numberNoExt)) {
            
            
            
            return $withFormatting ?
                $this->format($numberNoExt, PhoneNumberFormat::INTERNATIONAL) :
                $this->format($numberNoExt, PhoneNumberFormat::E164);
        }
        return $withFormatting ? $formattedNumber : static::normalizeDiallableCharsOnly($formattedNumber);
    }

    
    public function formatNationalNumberWithCarrierCode(PhoneNumber $number, $carrierCode)
    {
        $countryCallingCode = $number->getCountryCode();
        $nationalSignificantNumber = $this->getNationalSignificantNumber($number);
        if (!$this->hasValidCountryCallingCode($countryCallingCode)) {
            return $nationalSignificantNumber;
        }

        
        
        
        $regionCode = $this->getRegionCodeForCountryCode($countryCallingCode);
        
        $metadata = $this->getMetadataForRegionOrCallingCode($countryCallingCode, $regionCode);

        $formattedNumber = $this->formatNsn(
            $nationalSignificantNumber,
            $metadata,
            PhoneNumberFormat::NATIONAL,
            $carrierCode
        );
        $this->maybeAppendFormattedExtension($number, $metadata, PhoneNumberFormat::NATIONAL, $formattedNumber);
        $this->prefixNumberWithCountryCallingCode(
            $countryCallingCode,
            PhoneNumberFormat::NATIONAL,
            $formattedNumber
        );
        return $formattedNumber;
    }

    
    public function formatNationalNumberWithPreferredCarrierCode(PhoneNumber $number, $fallbackCarrierCode)
    {
        return $this->formatNationalNumberWithCarrierCode(
            $number,
            
            
            
            $number->getPreferredDomesticCarrierCode() != ''
                ? $number->getPreferredDomesticCarrierCode()
                : $fallbackCarrierCode
        );
    }

    
    public function canBeInternationallyDialled(PhoneNumber $number)
    {
        $metadata = $this->getMetadataForRegion($this->getRegionCodeForNumber($number));
        if ($metadata === null) {
            
            
            return true;
        }
        $nationalSignificantNumber = $this->getNationalSignificantNumber($number);
        return !$this->isNumberMatchingDesc($nationalSignificantNumber, $metadata->getNoInternationalDialling());
    }

    
    public static function normalizeDiallableCharsOnly($number)
    {
        if (count(static::$DIALLABLE_CHAR_MAPPINGS) === 0) {
            static::initDiallableCharMappings();
        }

        return static::normalizeHelper($number, static::$DIALLABLE_CHAR_MAPPINGS, true );
    }

    
    public function formatOutOfCountryKeepingAlphaChars(PhoneNumber $number, $regionCallingFrom)
    {
        $rawInput = $number->getRawInput();
        
        
        if ($rawInput === null || $rawInput === '') {
            return $this->formatOutOfCountryCallingNumber($number, $regionCallingFrom);
        }
        $countryCode = $number->getCountryCode();
        if (!$this->hasValidCountryCallingCode($countryCode)) {
            return $rawInput;
        }
        
        
        
        
        $rawInput = self::normalizeHelper($rawInput, static::$ALL_PLUS_NUMBER_GROUPING_SYMBOLS, true);
        
        
        
        
        $nationalNumber = $this->getNationalSignificantNumber($number);
        if (mb_strlen($nationalNumber) > 3) {
            $firstNationalNumberDigit = strpos($rawInput, substr($nationalNumber, 0, 3));
            if ($firstNationalNumberDigit !== false) {
                $rawInput = substr($rawInput, $firstNationalNumberDigit);
            }
        }
        $metadataForRegionCallingFrom = $this->getMetadataForRegion($regionCallingFrom);
        if ($countryCode == static::NANPA_COUNTRY_CODE) {
            if ($this->isNANPACountry($regionCallingFrom)) {
                return $countryCode . ' ' . $rawInput;
            }
        } elseif ($metadataForRegionCallingFrom !== null &&
            $countryCode == $this->getCountryCodeForValidRegion($regionCallingFrom)
        ) {
            $formattingPattern =
                $this->chooseFormattingPatternForNumber(
                    $metadataForRegionCallingFrom->numberFormats(),
                    $nationalNumber
                );
            if ($formattingPattern === null) {
                
                return $rawInput;
            }
            $newFormat = new NumberFormat();
            $newFormat->mergeFrom($formattingPattern);
            
            $newFormat->setPattern("(\\d+)(.*)");
            
            $newFormat->setFormat('$1$2');
            
            
            
            
            
            return $this->formatNsnUsingPattern($rawInput, $newFormat, PhoneNumberFormat::NATIONAL);
        }
        $internationalPrefixForFormatting = '';
        
        
        
        if ($metadataForRegionCallingFrom !== null) {
            $internationalPrefix = $metadataForRegionCallingFrom->getInternationalPrefix();
            $uniqueInternationalPrefixMatcher = new Matcher(static::SINGLE_INTERNATIONAL_PREFIX, $internationalPrefix);
            $internationalPrefixForFormatting =
                $uniqueInternationalPrefixMatcher->matches()
                    ? $internationalPrefix
                    : $metadataForRegionCallingFrom->getPreferredInternationalPrefix();
        }
        $formattedNumber = $rawInput;
        $regionCode = $this->getRegionCodeForCountryCode($countryCode);
        
        $metadataForRegion = $this->getMetadataForRegionOrCallingCode($countryCode, $regionCode);
        $this->maybeAppendFormattedExtension(
            $number,
            $metadataForRegion,
            PhoneNumberFormat::INTERNATIONAL,
            $formattedNumber
        );
        if ($internationalPrefixForFormatting != '') {
            $formattedNumber = $internationalPrefixForFormatting . ' ' . $countryCode . ' ' . $formattedNumber;
        } else {
            
            
            $this->prefixNumberWithCountryCallingCode(
                $countryCode,
                PhoneNumberFormat::INTERNATIONAL,
                $formattedNumber
            );
        }
        return $formattedNumber;
    }

    
    public function formatOutOfCountryCallingNumber(PhoneNumber $number, $regionCallingFrom)
    {
        if (!$this->isValidRegionCode($regionCallingFrom)) {
            return $this->format($number, PhoneNumberFormat::INTERNATIONAL);
        }
        $countryCallingCode = $number->getCountryCode();
        $nationalSignificantNumber = $this->getNationalSignificantNumber($number);
        if (!$this->hasValidCountryCallingCode($countryCallingCode)) {
            return $nationalSignificantNumber;
        }
        if ($countryCallingCode == static::NANPA_COUNTRY_CODE) {
            if ($this->isNANPACountry($regionCallingFrom)) {
                
                
                return $countryCallingCode . ' ' . $this->format($number, PhoneNumberFormat::NATIONAL);
            }
        } elseif ($countryCallingCode == $this->getCountryCodeForValidRegion($regionCallingFrom)) {
            
            
            
            
            
            
            return $this->format($number, PhoneNumberFormat::NATIONAL);
        }
        
        
        $metadataForRegionCallingFrom = $this->getMetadataForRegion($regionCallingFrom);

        $internationalPrefix = $metadataForRegionCallingFrom->getInternationalPrefix();

        
        
        
        $internationalPrefixForFormatting = '';
        if ($metadataForRegionCallingFrom->hasPreferredInternationalPrefix()) {
            $internationalPrefixForFormatting = $metadataForRegionCallingFrom->getPreferredInternationalPrefix();
        } else {
            $uniqueInternationalPrefixMatcher = new Matcher(static::SINGLE_INTERNATIONAL_PREFIX, $internationalPrefix);

            if ($uniqueInternationalPrefixMatcher->matches()) {
                $internationalPrefixForFormatting = $internationalPrefix;
            }
        }

        $regionCode = $this->getRegionCodeForCountryCode($countryCallingCode);
        
        
        $metadataForRegion = $this->getMetadataForRegionOrCallingCode($countryCallingCode, $regionCode);
        $formattedNationalNumber = $this->formatNsn(
            $nationalSignificantNumber,
            $metadataForRegion,
            PhoneNumberFormat::INTERNATIONAL
        );
        $formattedNumber = $formattedNationalNumber;
        $this->maybeAppendFormattedExtension(
            $number,
            $metadataForRegion,
            PhoneNumberFormat::INTERNATIONAL,
            $formattedNumber
        );
        if ($internationalPrefixForFormatting !== '') {
            $formattedNumber = $internationalPrefixForFormatting . ' ' . $countryCallingCode . ' ' . $formattedNumber;
        } else {
            $this->prefixNumberWithCountryCallingCode(
                $countryCallingCode,
                PhoneNumberFormat::INTERNATIONAL,
                $formattedNumber
            );
        }
        return $formattedNumber;
    }

    
    public function isNANPACountry($regionCode)
    {
        return in_array(strtoupper((string)$regionCode), $this->nanpaRegions);
    }

    
    public function formatInOriginalFormat(PhoneNumber $number, $regionCallingFrom)
    {
        if ($number->hasRawInput() && !$this->hasFormattingPatternForNumber($number)) {
            
            
            return $number->getRawInput();
        }
        if (!$number->hasCountryCodeSource()) {
            return $this->format($number, PhoneNumberFormat::NATIONAL);
        }
        switch ($number->getCountryCodeSource()) {
            case CountryCodeSource::FROM_NUMBER_WITH_PLUS_SIGN:
                $formattedNumber = $this->format($number, PhoneNumberFormat::INTERNATIONAL);
                break;
            case CountryCodeSource::FROM_NUMBER_WITH_IDD:
                $formattedNumber = $this->formatOutOfCountryCallingNumber($number, $regionCallingFrom);
                break;
            case CountryCodeSource::FROM_NUMBER_WITHOUT_PLUS_SIGN:
                $formattedNumber = substr($this->format($number, PhoneNumberFormat::INTERNATIONAL), 1);
                break;
            case CountryCodeSource::FROM_DEFAULT_COUNTRY:
                
            default:

                $regionCode = $this->getRegionCodeForCountryCode($number->getCountryCode());
                
                
                $nationalPrefix = $this->getNddPrefixForRegion($regionCode, true );
                $nationalFormat = $this->format($number, PhoneNumberFormat::NATIONAL);
                if ($nationalPrefix === null || $nationalPrefix === '') {
                    
                    
                    $formattedNumber = $nationalFormat;
                    break;
                }
                
                if ($this->rawInputContainsNationalPrefix(
                    $number->getRawInput(),
                    $nationalPrefix,
                    $regionCode
                )
                ) {
                    
                    $formattedNumber = $nationalFormat;
                    break;
                }
                
                
                $metadata = $this->getMetadataForRegion($regionCode);
                $nationalNumber = $this->getNationalSignificantNumber($number);
                $formatRule = $this->chooseFormattingPatternForNumber($metadata->numberFormats(), $nationalNumber);
                
                
                
                if ($formatRule === null) {
                    $formattedNumber = $nationalFormat;
                    break;
                }
                
                
                
                $candidateNationalPrefixRule = $formatRule->getNationalPrefixFormattingRule();
                
                $indexOfFirstGroup = strpos($candidateNationalPrefixRule, '$1');
                if ($indexOfFirstGroup <= 0) {
                    $formattedNumber = $nationalFormat;
                    break;
                }
                $candidateNationalPrefixRule = substr($candidateNationalPrefixRule, 0, $indexOfFirstGroup);
                $candidateNationalPrefixRule = static::normalizeDigitsOnly($candidateNationalPrefixRule);
                if ($candidateNationalPrefixRule === '') {
                    
                    $formattedNumber = $nationalFormat;
                    break;
                }
                
                $numFormatCopy = new NumberFormat();
                $numFormatCopy->mergeFrom($formatRule);
                $numFormatCopy->clearNationalPrefixFormattingRule();
                $numberFormats = array();
                $numberFormats[] = $numFormatCopy;
                $formattedNumber = $this->formatByPattern($number, PhoneNumberFormat::NATIONAL, $numberFormats);
                break;
        }
        $rawInput = $number->getRawInput();
        
        
        if ($formattedNumber !== null && mb_strlen($rawInput) > 0) {
            $normalizedFormattedNumber = static::normalizeDiallableCharsOnly($formattedNumber);
            $normalizedRawInput = static::normalizeDiallableCharsOnly($rawInput);
            if ($normalizedFormattedNumber != $normalizedRawInput) {
                $formattedNumber = $rawInput;
            }
        }
        return $formattedNumber;
    }

    
    protected function hasFormattingPatternForNumber(PhoneNumber $number)
    {
        $countryCallingCode = $number->getCountryCode();
        $phoneNumberRegion = $this->getRegionCodeForCountryCode($countryCallingCode);
        $metadata = $this->getMetadataForRegionOrCallingCode($countryCallingCode, $phoneNumberRegion);
        if ($metadata === null) {
            return false;
        }
        $nationalNumber = $this->getNationalSignificantNumber($number);
        $formatRule = $this->chooseFormattingPatternForNumber($metadata->numberFormats(), $nationalNumber);
        return $formatRule !== null;
    }

    
    public function getNddPrefixForRegion($regionCode, $stripNonDigits)
    {
        $metadata = $this->getMetadataForRegion($regionCode);
        if ($metadata === null) {
            return null;
        }
        $nationalPrefix = $metadata->getNationalPrefix();
        
        if ($nationalPrefix == '') {
            return null;
        }
        if ($stripNonDigits) {
            
            
            $nationalPrefix = str_replace('~', '', $nationalPrefix);
        }
        return $nationalPrefix;
    }

    
    protected function rawInputContainsNationalPrefix($rawInput, $nationalPrefix, $regionCode)
    {
        $normalizedNationalNumber = static::normalizeDigitsOnly($rawInput);
        if (strpos($normalizedNationalNumber, $nationalPrefix) === 0) {
            try {
                
                
                
                
                return $this->isValidNumber(
                    $this->parse(substr($normalizedNationalNumber, mb_strlen($nationalPrefix)), $regionCode)
                );
            } catch (NumberParseException $e) {
                return false;
            }
        }
        return false;
    }

    
    public function isValidNumber(PhoneNumber $number)
    {
        $regionCode = $this->getRegionCodeForNumber($number);
        return $this->isValidNumberForRegion($number, $regionCode);
    }

    
    public function isValidNumberForRegion(PhoneNumber $number, $regionCode)
    {
        $countryCode = $number->getCountryCode();
        $metadata = $this->getMetadataForRegionOrCallingCode($countryCode, $regionCode);
        if (($metadata === null) ||
            (static::REGION_CODE_FOR_NON_GEO_ENTITY !== $regionCode &&
                $countryCode !== $this->getCountryCodeForValidRegion($regionCode))
        ) {
            
            
            return false;
        }
        $nationalSignificantNumber = $this->getNationalSignificantNumber($number);

        return $this->getNumberTypeHelper($nationalSignificantNumber, $metadata) != PhoneNumberType::UNKNOWN;
    }

    
    public function parse($numberToParse, $defaultRegion = null, PhoneNumber $phoneNumber = null, $keepRawInput = false)
    {
        if ($phoneNumber === null) {
            $phoneNumber = new PhoneNumber();
        }
        $this->parseHelper($numberToParse, $defaultRegion, $keepRawInput, true, $phoneNumber);
        return $phoneNumber;
    }

    
    public function formatByPattern(PhoneNumber $number, $numberFormat, array $userDefinedFormats)
    {
        $countryCallingCode = $number->getCountryCode();
        $nationalSignificantNumber = $this->getNationalSignificantNumber($number);
        if (!$this->hasValidCountryCallingCode($countryCallingCode)) {
            return $nationalSignificantNumber;
        }
        
        
        
        $regionCode = $this->getRegionCodeForCountryCode($countryCallingCode);
        
        $metadata = $this->getMetadataForRegionOrCallingCode($countryCallingCode, $regionCode);

        $formattedNumber = '';

        $formattingPattern = $this->chooseFormattingPatternForNumber($userDefinedFormats, $nationalSignificantNumber);
        if ($formattingPattern === null) {
            
            $formattedNumber .= $nationalSignificantNumber;
        } else {
            $numFormatCopy = new NumberFormat();
            
            
            
            $numFormatCopy->mergeFrom($formattingPattern);
            $nationalPrefixFormattingRule = $formattingPattern->getNationalPrefixFormattingRule();
            if ($nationalPrefixFormattingRule !== '') {
                $nationalPrefix = $metadata->getNationalPrefix();
                if ($nationalPrefix != '') {
                    
                    $nationalPrefixFormattingRule = str_replace(
                        array(static::NP_STRING, static::FG_STRING),
                        array($nationalPrefix, '$1'),
                        $nationalPrefixFormattingRule
                    );
                    $numFormatCopy->setNationalPrefixFormattingRule($nationalPrefixFormattingRule);
                } else {
                    
                    $numFormatCopy->clearNationalPrefixFormattingRule();
                }
            }
            $formattedNumber .= $this->formatNsnUsingPattern($nationalSignificantNumber, $numFormatCopy, $numberFormat);
        }
        $this->maybeAppendFormattedExtension($number, $metadata, $numberFormat, $formattedNumber);
        $this->prefixNumberWithCountryCallingCode($countryCallingCode, $numberFormat, $formattedNumber);
        return $formattedNumber;
    }

    
    public function getExampleNumber($regionCode)
    {
        return $this->getExampleNumberForType($regionCode, PhoneNumberType::FIXED_LINE);
    }

    
    public function getInvalidExampleNumber($regionCode)
    {
        if (!$this->isValidRegionCode($regionCode)) {
            return null;
        }

        
        
        
        

        $desc = $this->getNumberDescByType($this->getMetadataForRegion($regionCode), PhoneNumberType::FIXED_LINE);

        if ($desc->getExampleNumber() == '') {
            
            return null;
        }

        $exampleNumber = $desc->getExampleNumber();

        
        
        
        
        
        
        
        
        
        
        
        for ($phoneNumberLength = mb_strlen($exampleNumber) - 1; $phoneNumberLength >= static::MIN_LENGTH_FOR_NSN; $phoneNumberLength--) {
            $numberToTry = mb_substr($exampleNumber, 0, $phoneNumberLength);
            try {
                $possiblyValidNumber = $this->parse($numberToTry, $regionCode);
                if (!$this->isValidNumber($possiblyValidNumber)) {
                    return $possiblyValidNumber;
                }
            } catch (NumberParseException $e) {
                
                
            }
        }
        
        return null;
    }

    
    public function getExampleNumberForType($regionCodeOrType, $type = null)
    {
        if ($regionCodeOrType !== null && $type === null) {
            
            foreach ($this->getSupportedRegions() as $regionCode) {
                $exampleNumber = $this->getExampleNumberForType($regionCode, $regionCodeOrType);
                if ($exampleNumber !== null) {
                    return $exampleNumber;
                }
            }

            
            foreach ($this->getSupportedGlobalNetworkCallingCodes() as $countryCallingCode) {
                $desc = $this->getNumberDescByType($this->getMetadataForNonGeographicalRegion($countryCallingCode), $regionCodeOrType);
                try {
                    if ($desc->getExampleNumber() != '') {
                        return $this->parse('+' . $countryCallingCode . $desc->getExampleNumber(), static::UNKNOWN_REGION);
                    }
                } catch (NumberParseException $e) {
                    
                }
            }
            
            return null;
        }

        
        if (!$this->isValidRegionCode($regionCodeOrType)) {
            return null;
        }
        $desc = $this->getNumberDescByType($this->getMetadataForRegion($regionCodeOrType), $type);
        try {
            if ($desc->hasExampleNumber()) {
                return $this->parse($desc->getExampleNumber(), $regionCodeOrType);
            }
        } catch (NumberParseException $e) {
            
        }
        return null;
    }

    
    protected function getNumberDescByType(PhoneMetadata $metadata, $type)
    {
        switch ($type) {
            case PhoneNumberType::PREMIUM_RATE:
                return $metadata->getPremiumRate();
            case PhoneNumberType::TOLL_FREE:
                return $metadata->getTollFree();
            case PhoneNumberType::MOBILE:
                return $metadata->getMobile();
            case PhoneNumberType::FIXED_LINE:
            case PhoneNumberType::FIXED_LINE_OR_MOBILE:
                return $metadata->getFixedLine();
            case PhoneNumberType::SHARED_COST:
                return $metadata->getSharedCost();
            case PhoneNumberType::VOIP:
                return $metadata->getVoip();
            case PhoneNumberType::PERSONAL_NUMBER:
                return $metadata->getPersonalNumber();
            case PhoneNumberType::PAGER:
                return $metadata->getPager();
            case PhoneNumberType::UAN:
                return $metadata->getUan();
            case PhoneNumberType::VOICEMAIL:
                return $metadata->getVoicemail();
            default:
                return $metadata->getGeneralDesc();
        }
    }

    
    public function getExampleNumberForNonGeoEntity($countryCallingCode)
    {
        $metadata = $this->getMetadataForNonGeographicalRegion($countryCallingCode);
        if ($metadata !== null) {
            
            
            
            
            
            $list = array(
                $metadata->getMobile(),
                $metadata->getTollFree(),
                $metadata->getSharedCost(),
                $metadata->getVoip(),
                $metadata->getVoicemail(),
                $metadata->getUan(),
                $metadata->getPremiumRate(),
            );
            foreach ($list as $desc) {
                try {
                    if ($desc !== null && $desc->hasExampleNumber()) {
                        return $this->parse('+' . $countryCallingCode . $desc->getExampleNumber(), self::UNKNOWN_REGION);
                    }
                } catch (NumberParseException $e) {
                    
                }
            }
        }
        return null;
    }


    
    public function isNumberMatch($firstNumberIn, $secondNumberIn)
    {
        if (is_string($firstNumberIn) && is_string($secondNumberIn)) {
            try {
                $firstNumberAsProto = $this->parse($firstNumberIn, static::UNKNOWN_REGION);
                return $this->isNumberMatch($firstNumberAsProto, $secondNumberIn);
            } catch (NumberParseException $e) {
                if ($e->getErrorType() === NumberParseException::INVALID_COUNTRY_CODE) {
                    try {
                        $secondNumberAsProto = $this->parse($secondNumberIn, static::UNKNOWN_REGION);
                        return $this->isNumberMatch($secondNumberAsProto, $firstNumberIn);
                    } catch (NumberParseException $e2) {
                        if ($e2->getErrorType() === NumberParseException::INVALID_COUNTRY_CODE) {
                            try {
                                $firstNumberProto = new PhoneNumber();
                                $secondNumberProto = new PhoneNumber();
                                $this->parseHelper($firstNumberIn, null, false, false, $firstNumberProto);
                                $this->parseHelper($secondNumberIn, null, false, false, $secondNumberProto);
                                return $this->isNumberMatch($firstNumberProto, $secondNumberProto);
                            } catch (NumberParseException $e3) {
                                
                            }
                        }
                    }
                }
            }
            return MatchType::NOT_A_NUMBER;
        }
        if ($firstNumberIn instanceof PhoneNumber && is_string($secondNumberIn)) {
            
            
            try {
                $secondNumberAsProto = $this->parse($secondNumberIn, static::UNKNOWN_REGION);
                return $this->isNumberMatch($firstNumberIn, $secondNumberAsProto);
            } catch (NumberParseException $e) {
                if ($e->getErrorType() === NumberParseException::INVALID_COUNTRY_CODE) {
                    
                    
                    
                    $firstNumberRegion = $this->getRegionCodeForCountryCode($firstNumberIn->getCountryCode());
                    try {
                        if ($firstNumberRegion != static::UNKNOWN_REGION) {
                            $secondNumberWithFirstNumberRegion = $this->parse($secondNumberIn, $firstNumberRegion);
                            $match = $this->isNumberMatch($firstNumberIn, $secondNumberWithFirstNumberRegion);
                            if ($match === MatchType::EXACT_MATCH) {
                                return MatchType::NSN_MATCH;
                            }
                            return $match;
                        }

                        
                        
                        $secondNumberProto = new PhoneNumber();
                        $this->parseHelper($secondNumberIn, null, false, false, $secondNumberProto);
                        return $this->isNumberMatch($firstNumberIn, $secondNumberProto);
                    } catch (NumberParseException $e2) {
                        
                    }
                }
            }
        }
        if ($firstNumberIn instanceof PhoneNumber && $secondNumberIn instanceof PhoneNumber) {
            
            
            $firstNumber = self::copyCoreFieldsOnly($firstNumberIn);
            $secondNumber = self::copyCoreFieldsOnly($secondNumberIn);

            
            if ($firstNumber->hasExtension() && $secondNumber->hasExtension() &&
                $firstNumber->getExtension() != $secondNumber->getExtension()
            ) {
                return MatchType::NO_MATCH;
            }

            $firstNumberCountryCode = $firstNumber->getCountryCode();
            $secondNumberCountryCode = $secondNumber->getCountryCode();
            
            if ($firstNumberCountryCode != 0 && $secondNumberCountryCode != 0) {
                if ($firstNumber->equals($secondNumber)) {
                    return MatchType::EXACT_MATCH;
                }

                if ($firstNumberCountryCode == $secondNumberCountryCode &&
                    $this->isNationalNumberSuffixOfTheOther($firstNumber, $secondNumber)) {
                    
                    
                    
                    return MatchType::SHORT_NSN_MATCH;
                }
                
                return MatchType::NO_MATCH;
            }
            
            
            $firstNumber->setCountryCode($secondNumberCountryCode);
            
            if ($firstNumber->equals($secondNumber)) {
                return MatchType::NSN_MATCH;
            }
            if ($this->isNationalNumberSuffixOfTheOther($firstNumber, $secondNumber)) {
                return MatchType::SHORT_NSN_MATCH;
            }
            return MatchType::NO_MATCH;
        }
        return MatchType::NOT_A_NUMBER;
    }

    
    protected function isNationalNumberSuffixOfTheOther(PhoneNumber $firstNumber, PhoneNumber $secondNumber)
    {
        $firstNumberNationalNumber = trim((string)$firstNumber->getNationalNumber());
        $secondNumberNationalNumber = trim((string)$secondNumber->getNationalNumber());
        return $this->stringEndsWithString($firstNumberNationalNumber, $secondNumberNationalNumber) ||
        $this->stringEndsWithString($secondNumberNationalNumber, $firstNumberNationalNumber);
    }

    
    protected function stringEndsWithString($hayStack, $needle)
    {
        $revNeedle = strrev($needle);
        $revHayStack = strrev($hayStack);
        return strpos($revHayStack, $revNeedle) === 0;
    }

    
    public function isMobileNumberPortableRegion($regionCode)
    {
        $metadata = $this->getMetadataForRegion($regionCode);
        if ($metadata === null) {
            return false;
        }

        return $metadata->isMobileNumberPortableRegion();
    }

    
    public function isPossibleNumber($number, $regionDialingFrom = null)
    {
        if (is_string($number)) {
            try {
                return $this->isPossibleNumber($this->parse($number, $regionDialingFrom));
            } catch (NumberParseException $e) {
                return false;
            }
        } else {
            $result = $this->isPossibleNumberWithReason($number);
            return $result === ValidationResult::IS_POSSIBLE
                || $result === ValidationResult::IS_POSSIBLE_LOCAL_ONLY;
        }
    }


    
    public function isPossibleNumberWithReason(PhoneNumber $number)
    {
        return $this->isPossibleNumberForTypeWithReason($number, PhoneNumberType::UNKNOWN);
    }

    
    public function isPossibleNumberForTypeWithReason(PhoneNumber $number, $type)
    {
        $nationalNumber = $this->getNationalSignificantNumber($number);
        $countryCode = $number->getCountryCode();

        
        
        
        
        
        
        if (!$this->hasValidCountryCallingCode($countryCode)) {
            return ValidationResult::INVALID_COUNTRY_CODE;
        }

        $regionCode = $this->getRegionCodeForCountryCode($countryCode);
        
        $metadata = $this->getMetadataForRegionOrCallingCode($countryCode, $regionCode);
        return $this->testNumberLength($nationalNumber, $metadata, $type);
    }

    
    public function truncateTooLongNumber(PhoneNumber $number)
    {
        if ($this->isValidNumber($number)) {
            return true;
        }
        $numberCopy = new PhoneNumber();
        $numberCopy->mergeFrom($number);
        $nationalNumber = $number->getNationalNumber();
        do {
            $nationalNumber = floor($nationalNumber / 10);
            $numberCopy->setNationalNumber($nationalNumber);
            if ($this->isPossibleNumberWithReason($numberCopy) == ValidationResult::TOO_SHORT || $nationalNumber == 0) {
                return false;
            }
        } while (!$this->isValidNumber($numberCopy));
        $number->setNationalNumber($nationalNumber);
        return true;
    }
}

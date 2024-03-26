<?php

namespace libphonenumber;

use libphonenumber\Leniency\AbstractLeniency;


class PhoneNumberMatcher implements \Iterator
{
    protected static $initialized = false;

    
    protected static $pattern;

    
    protected static $pubPages = "\\d{1,5}-+\\d{1,5}\\s{0,4}\\(\\d{1,4}";

    
    protected static $slashSeparatedDates = "(?:(?:[0-3]?\\d/[01]?\\d)|(?:[01]?\\d/[0-3]?\\d))/(?:[12]\\d)?\\d{2}";

    
    protected static $timeStamps = "[12]\\d{3}[-/]?[01]\\d[-/]?[0-3]\\d +[0-2]\\d$";
    protected static $timeStampsSuffix = ":[0-5]\\d";

    
    protected static $matchingBrackets;

    
    protected static $innerMatches = array();

    
    protected static $leadClass;

    
    protected static $alternateFormatsFilePrefix;

    protected static function init()
    {
        static::$alternateFormatsFilePrefix = __DIR__ . '/data/PhoneNumberAlternateFormats';

        static::$innerMatches = array(
            
            '/+(.*)',
            
            
            "(\\([^(]*)",
            
            
            "(?:\\p{Z}-|-\\p{Z})\\p{Z}*(.+)",
            
            
            
            "[‒-―－]\\p{Z}*(.+)",
            
            "\\.+\\p{Z}*([^.]+)",
            
            "\\p{Z}+(\\P{Z}+)"
        );

        

        $openingParens = "(\\[\xEF\xBC\x88\xEF\xBC\xBB";
        $closingParens = ")\\]\xEF\xBC\x89\xEF\xBC\xBD";
        $nonParens = '[^' . $openingParens . $closingParens . ']';

        
        $bracketPairLimit = static::limit(0, 3);

        
        static::$matchingBrackets =
            '(?:[' . $openingParens . '])?' . '(?:' . $nonParens . '+' . '[' . $closingParens . '])?'
            . $nonParens . '+'
            . '(?:[' . $openingParens . ']' . $nonParens . '+[' . $closingParens . '])' . $bracketPairLimit
            . $nonParens . '*';

        
        $leadLimit = static::limit(0, 2);

        
        $punctuationLimit = static::limit(0, 4);

        
        $digitBlockLimit = PhoneNumberUtil::MAX_LENGTH_FOR_NSN + PhoneNumberUtil::MAX_LENGTH_COUNTRY_CODE;

        
        $blockLimit = static::limit(0, $digitBlockLimit);

        
        $punctuation = '[' . PhoneNumberUtil::VALID_PUNCTUATION . ']' . $punctuationLimit;

        
        $digitSequence = "\\p{Nd}" . static::limit(1, $digitBlockLimit);


        $leadClassChars = $openingParens . PhoneNumberUtil::PLUS_CHARS;
        $leadClass = '[' . $leadClassChars . ']';
        static::$leadClass = $leadClass;

        
        PhoneNumberUtil::initExtnPatterns();

        
        static::$pattern = '(?:' . $leadClass . $punctuation . ')' . $leadLimit
            . $digitSequence . '(?:' . $punctuation . $digitSequence . ')' . $blockLimit
            . '(?:' . PhoneNumberUtil::$EXTN_PATTERNS_FOR_MATCHING . ')?';

        static::$initialized = true;
    }

    
    protected static function limit($lower, $upper)
    {
        if (($lower < 0) || ($upper <= 0) || ($upper < $lower)) {
            throw new \InvalidArgumentException();
        }

        return '{' . $lower . ',' . $upper . '}';
    }

    
    protected $phoneUtil;

    
    protected $text;

    
    protected $preferredRegion;

    
    protected $leniency;

    
    protected $maxTries;

    
    protected $state = 'NOT_READY';

    
    protected $lastMatch;

    
    protected $searchIndex = 0;

    
    public function __construct(PhoneNumberUtil $util, $text, $country, AbstractLeniency $leniency, $maxTries)
    {
        if ($maxTries < 0) {
            throw new \InvalidArgumentException();
        }

        $this->phoneUtil = $util;
        $this->text = ($text !== null) ? $text : '';
        $this->preferredRegion = $country;
        $this->leniency = $leniency;
        $this->maxTries = $maxTries;

        if (static::$initialized === false) {
            static::init();
        }
    }

    
    protected function find($index)
    {
        $matcher = new Matcher(static::$pattern, $this->text);
        while (($this->maxTries > 0) && $matcher->find($index)) {
            $start = $matcher->start();
            $cutLength = $matcher->end() - $start;
            $candidate = \mb_substr($this->text, $start, $cutLength);

            
            
            
            $candidate = static::trimAfterFirstMatch(PhoneNumberUtil::$SECOND_NUMBER_START_PATTERN, $candidate);

            $match = $this->extractMatch($candidate, $start);
            if ($match !== null) {
                return $match;
            }

            $index = $start + \mb_strlen($candidate);
            $this->maxTries--;
        }

        return null;
    }

    
    protected static function trimAfterFirstMatch($pattern, $candidate)
    {
        $trailingCharsMatcher = new Matcher($pattern, $candidate);
        if ($trailingCharsMatcher->find()) {
            $startChar = $trailingCharsMatcher->start();
            $candidate = \mb_substr($candidate, 0, $startChar);
        }
        return $candidate;
    }

    
    public static function isLatinLetter($letter)
    {
        
        if (\preg_match('/\p{L}/u', $letter) !== 1 && \preg_match('/\p{Mn}/u', $letter) !== 1) {
            return false;
        }

        return (\preg_match('/\p{Latin}/u', $letter) === 1)
        || (\preg_match('/\pM+/u', $letter) === 1);
    }

    
    protected static function isInvalidPunctuationSymbol($character)
    {
        return $character == '%' || \preg_match('/\p{Sc}/u', $character);
    }

    
    protected function extractMatch($candidate, $offset)
    {
        
        $dateMatcher = new Matcher(static::$slashSeparatedDates, $candidate);
        if ($dateMatcher->find()) {
            return null;
        }

        
        $timeStampMatcher = new Matcher(static::$timeStamps, $candidate);
        if ($timeStampMatcher->find()) {
            $followingText = \mb_substr($this->text, $offset + \mb_strlen($candidate));
            $timeStampSuffixMatcher = new Matcher(static::$timeStampsSuffix, $followingText);
            if ($timeStampSuffixMatcher->lookingAt()) {
                return null;
            }
        }

        
        $match = $this->parseAndVerify($candidate, $offset);
        if ($match !== null) {
            return $match;
        }

        
        
        return $this->extractInnerMatch($candidate, $offset);
    }

    
    protected function extractInnerMatch($candidate, $offset)
    {
        foreach (static::$innerMatches as $possibleInnerMatch) {
            $groupMatcher = new Matcher($possibleInnerMatch, $candidate);
            $isFirstMatch = true;

            while ($groupMatcher->find() && $this->maxTries > 0) {
                if ($isFirstMatch) {
                    
                    $group = static::trimAfterFirstMatch(
                        PhoneNumberUtil::$UNWANTED_END_CHAR_PATTERN,
                        \mb_substr($candidate, 0, $groupMatcher->start())
                    );

                    $match = $this->parseAndVerify($group, $offset);
                    if ($match !== null) {
                        return $match;
                    }
                    $this->maxTries--;
                    $isFirstMatch = false;
                }
                $group = static::trimAfterFirstMatch(
                    PhoneNumberUtil::$UNWANTED_END_CHAR_PATTERN,
                    $groupMatcher->group(1)
                );
                $match = $this->parseAndVerify($group, $offset + $groupMatcher->start(1));
                if ($match !== null) {
                    return $match;
                }
                $this->maxTries--;
            }
        }
        return null;
    }

    
    protected function parseAndVerify($candidate, $offset)
    {
        try {
            
            
            $matchingBracketsMatcher = new Matcher(static::$matchingBrackets, $candidate);
            $pubPagesMatcher = new Matcher(static::$pubPages, $candidate);
            if (!$matchingBracketsMatcher->matches() || $pubPagesMatcher->find()) {
                return null;
            }

            
            
            if ($this->leniency->compareTo(Leniency::VALID()) >= 0) {
                
                
                $leadClassMatcher = new Matcher(static::$leadClass, $candidate);
                if ($offset > 0 && !$leadClassMatcher->lookingAt()) {
                    $previousChar = \mb_substr($this->text, $offset - 1, 1);
                    
                    if (static::isInvalidPunctuationSymbol($previousChar) || static::isLatinLetter($previousChar)) {
                        return null;
                    }
                }
                $lastCharIndex = $offset + \mb_strlen($candidate);
                if ($lastCharIndex < \mb_strlen($this->text)) {
                    $nextChar = \mb_substr($this->text, $lastCharIndex, 1);
                    if (static::isInvalidPunctuationSymbol($nextChar) || static::isLatinLetter($nextChar)) {
                        return null;
                    }
                }
            }

            $number = $this->phoneUtil->parseAndKeepRawInput($candidate, $this->preferredRegion);

            if ($this->leniency->verify($number, $candidate, $this->phoneUtil)) {
                
                
                
                $number->clearCountryCodeSource();
                $number->clearRawInput();
                $number->clearPreferredDomesticCarrierCode();
                return new PhoneNumberMatch($offset, $candidate, $number);
            }
        } catch (NumberParseException $e) {
            
        }
        return null;
    }

    
    public static function allNumberGroupsRemainGrouped(
        PhoneNumberUtil $util,
        PhoneNumber $number,
        $normalizedCandidate,
        $formattedNumberGroups
    ) {
        $fromIndex = 0;
        if ($number->getCountryCodeSource() !== CountryCodeSource::FROM_DEFAULT_COUNTRY) {
            
            $countryCode = $number->getCountryCode();
            $fromIndex = \mb_strpos($normalizedCandidate, $countryCode) + \mb_strlen($countryCode);
        }

        
        
        $formattedNumberGroupsLength = \count($formattedNumberGroups);
        for ($i = 0; $i < $formattedNumberGroupsLength; $i++) {
            
            
            $fromIndex = \mb_strpos($normalizedCandidate, $formattedNumberGroups[$i], $fromIndex);
            if ($fromIndex === false) {
                return false;
            }

            
            $fromIndex += \mb_strlen($formattedNumberGroups[$i]);
            if ($i === 0 && $fromIndex < \mb_strlen($normalizedCandidate)) {
                
                
                
                
                $region = $util->getRegionCodeForCountryCode($number->getCountryCode());

                if ($util->getNddPrefixForRegion($region, true) !== null
                    && \is_int(\mb_substr($normalizedCandidate, $fromIndex, 1))
                ) {
                    
                    
                    
                    $nationalSignificantNumber = $util->getNationalSignificantNumber($number);
                    return \mb_substr(
                        \mb_substr($normalizedCandidate, $fromIndex - \mb_strlen($formattedNumberGroups[$i])),
                        \mb_strlen($nationalSignificantNumber)
                    ) === $nationalSignificantNumber;
                }
            }
        }
        
        
        

        if ($number->hasExtension()) {
            return \mb_strpos(\mb_substr($normalizedCandidate, $fromIndex), $number->getExtension()) !== false;
        }

        return true;
    }

    
    public static function allNumberGroupsAreExactlyPresent(
        PhoneNumberUtil $util,
        PhoneNumber $number,
        $normalizedCandidate,
        $formattedNumberGroups
    ) {
        $candidateGroups = \preg_split(PhoneNumberUtil::NON_DIGITS_PATTERN, $normalizedCandidate);

        
        $candidateNumberGroupIndex = $number->hasExtension() ? \count($candidateGroups) - 2 : \count($candidateGroups) - 1;

        
        
        
        if (\count($candidateGroups) == 1
            || \mb_strpos(
                $candidateGroups[$candidateNumberGroupIndex],
                $util->getNationalSignificantNumber($number)
            ) !== false
        ) {
            return true;
        }

        
        
        for ($formattedNumberGroupIndex = (\count($formattedNumberGroups) - 1);
            $formattedNumberGroupIndex > 0 && $candidateNumberGroupIndex >= 0;
            $formattedNumberGroupIndex--, $candidateNumberGroupIndex--) {
            if ($candidateGroups[$candidateNumberGroupIndex] != $formattedNumberGroups[$formattedNumberGroupIndex]) {
                return false;
            }
        }

        
        
        return ($candidateNumberGroupIndex >= 0
            && \mb_substr(
                $candidateGroups[$candidateNumberGroupIndex],
                -\mb_strlen($formattedNumberGroups[0])
            ) == $formattedNumberGroups[0]);
    }

    
    protected static function getNationalNumberGroups(
        PhoneNumberUtil $util,
        PhoneNumber $number,
        NumberFormat $formattingPattern = null
    ) {
        if ($formattingPattern === null) {
            
            $rfc3966Format = $util->format($number, PhoneNumberFormat::RFC3966);
            
            
            $endIndex = \mb_strpos($rfc3966Format, ';');
            if ($endIndex === false) {
                $endIndex = \mb_strlen($rfc3966Format);
            }

            
            $startIndex = \mb_strpos($rfc3966Format, '-') + 1;
            return \explode('-', \mb_substr($rfc3966Format, $startIndex, $endIndex - $startIndex));
        }

        
        $nationalSignificantNumber = $util->getNationalSignificantNumber($number);
        return \explode('-', $util->formatNsnUsingPattern(
            $nationalSignificantNumber,
            $formattingPattern,
            PhoneNumberFormat::RFC3966
        ));
    }

    
    public static function checkNumberGroupingIsValid(
        PhoneNumber $number,
        $candidate,
        PhoneNumberUtil $util,
        \Closure $checker
    ) {
        $normalizedCandidate = PhoneNumberUtil::normalizeDigits($candidate, true );
        $formattedNumberGroups = static::getNationalNumberGroups($util, $number);
        if ($checker($util, $number, $normalizedCandidate, $formattedNumberGroups)) {
            return true;
        }

        
        $alternateFormats = static::getAlternateFormatsForCountry($number->getCountryCode());

        $nationalSignificantNumber = $util->getNationalSignificantNumber($number);
        if ($alternateFormats !== null) {
            foreach ($alternateFormats->numberFormats() as $alternateFormat) {
                if ($alternateFormat->leadingDigitsPatternSize() > 0) {
                    
                    $pattern = $alternateFormat->getLeadingDigitsPattern(0);

                    $nationalSignificantNumberMatcher = new Matcher($pattern, $nationalSignificantNumber);
                    if (!$nationalSignificantNumberMatcher->lookingAt()) {
                        
                        continue;
                    }
                }

                $formattedNumberGroups = static::getNationalNumberGroups($util, $number, $alternateFormat);
                if ($checker($util, $number, $normalizedCandidate, $formattedNumberGroups)) {
                    return true;
                }
            }
        }
        return false;
    }

    
    public static function containsMoreThanOneSlashInNationalNumber(PhoneNumber $number, $candidate)
    {
        $firstSlashInBodyIndex = \mb_strpos($candidate, '/');
        if ($firstSlashInBodyIndex === false) {
            
            return false;
        }

        
        $secondSlashInBodyIndex = \mb_strpos($candidate, '/', $firstSlashInBodyIndex + 1);
        if ($secondSlashInBodyIndex === false) {
            
            return false;
        }

        
        $candidateHasCountryCode = ($number->getCountryCodeSource() === CountryCodeSource::FROM_NUMBER_WITH_PLUS_SIGN
            || $number->getCountryCodeSource() === CountryCodeSource::FROM_NUMBER_WITHOUT_PLUS_SIGN);

        if ($candidateHasCountryCode
            && PhoneNumberUtil::normalizeDigitsOnly(
                \mb_substr($candidate, 0, $firstSlashInBodyIndex)
            ) == $number->getCountryCode()
        ) {
            
            return (\mb_strpos(\mb_substr($candidate, $secondSlashInBodyIndex + 1), '/') !== false);
        }

        return true;
    }

    
    public static function containsOnlyValidXChars(PhoneNumber $number, $candidate, PhoneNumberUtil $util)
    {
        
        
        
        
        
        $candidateLength = \mb_strlen($candidate);

        for ($index = 0; $index < $candidateLength - 1; $index++) {
            $charAtIndex = \mb_substr($candidate, $index, 1);
            if ($charAtIndex == 'x' || $charAtIndex == 'X') {
                $charAtNextIndex = \mb_substr($candidate, $index + 1, 1);
                if ($charAtNextIndex == 'x' || $charAtNextIndex == 'X') {
                    
                    
                    $index++;

                    if ($util->isNumberMatch($number, \mb_substr($candidate, $index)) != MatchType::NSN_MATCH) {
                        return false;
                    }
                } elseif (!PhoneNumberUtil::normalizeDigitsOnly(\mb_substr(
                    $candidate,
                    $index
                )) == $number->getExtension()
                ) {
                    
                    
                    return false;
                }
            }
        }
        return true;
    }

    
    public static function isNationalPrefixPresentIfRequired(PhoneNumber $number, PhoneNumberUtil $util)
    {
        
        
        if ($number->getCountryCodeSource() !== CountryCodeSource::FROM_DEFAULT_COUNTRY) {
            return true;
        }

        $phoneNumberRegion = $util->getRegionCodeForCountryCode($number->getCountryCode());
        $metadata = $util->getMetadataForRegion($phoneNumberRegion);
        if ($metadata === null) {
            return true;
        }

        
        $nationalNumber = $util->getNationalSignificantNumber($number);
        $formatRule = $util->chooseFormattingPatternForNumber($metadata->numberFormats(), $nationalNumber);
        
        
        if (($formatRule !== null) && $formatRule->getNationalPrefixFormattingRule() !== '') {
            if ($formatRule->getNationalPrefixOptionalWhenFormatting()) {
                
                
                return true;
            }

            if (PhoneNumberUtil::formattingRuleHasFirstGroupOnly($formatRule->getNationalPrefixFormattingRule())) {
                
                return true;
            }

            
            $rawInputCopy = PhoneNumberUtil::normalizeDigitsOnly($number->getRawInput());
            $rawInput = $rawInputCopy;
            
            
            $carrierCode = null;
            return $util->maybeStripNationalPrefixAndCarrierCode($rawInput, $metadata, $carrierCode);
        }
        return true;
    }


    
    protected static $callingCodeToAlternateFormatsMap = array();

    
    protected static function getAlternateFormatsForCountry($countryCallingCode)
    {
        $countryCodeSet = AlternateFormatsCountryCodeSet::$alternateFormatsCountryCodeSet;

        if (!\in_array($countryCallingCode, $countryCodeSet)) {
            return null;
        }

        if (!isset(static::$callingCodeToAlternateFormatsMap[$countryCallingCode])) {
            static::loadAlternateFormatsMetadataFromFile($countryCallingCode);
        }

        return static::$callingCodeToAlternateFormatsMap[$countryCallingCode];
    }

    
    protected static function loadAlternateFormatsMetadataFromFile($countryCallingCode)
    {
        $fileName = static::$alternateFormatsFilePrefix . '_' . $countryCallingCode . '.php';

        if (!\is_readable($fileName)) {
            throw new \Exception('missing metadata: ' . $fileName);
        }

        $metadataLoader = new DefaultMetadataLoader();
        $data = $metadataLoader->loadMetadata($fileName);
        $metadata = new PhoneMetadata();
        $metadata->fromArray($data);
        static::$callingCodeToAlternateFormatsMap[$countryCallingCode] = $metadata;
    }


    
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->lastMatch;
    }

    
    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->lastMatch = $this->find($this->searchIndex);

        if ($this->lastMatch === null) {
            $this->state = 'DONE';
        } else {
            $this->searchIndex = $this->lastMatch->end();
            $this->state = 'READY';
        }

        $this->searchIndex++;
    }

    
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->searchIndex;
    }

    
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->state === 'READY';
    }

    
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->searchIndex = 0;
        $this->next();
    }
}

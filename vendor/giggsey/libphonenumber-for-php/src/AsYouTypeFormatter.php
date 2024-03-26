<?php

namespace libphonenumber;


class AsYouTypeFormatter
{
    
    private $currentOutput;
    
    private $formattingTemplate;
    
    private $currentFormattingPattern;

    
    private $accruedInput;

    
    private $accruedInputWithoutFormatting;
    
    private $ableToFormat = true;

    
    private $inputHasFormatting = false;

    
    private $isCompleteNumber = false;

    
    private $isExpectingCountryCallingCode = false;

    
    private $phoneUtil;

    
    private $defaultCountry;

    
    private $defaultMetadata;
    
    private $currentMetadata;

    
    private $possibleFormats = array();

    
    private $lastMatchPosition = 0;
    
    private $originalPosition = 0;

    
    private $positionToRemember = 0;

    
    private $prefixBeforeNationalNumber = '';

    
    private $shouldAddSpaceAfterNationalPrefix = false;

    
    private $extractedNationalPrefix = '';

    
    private $nationalNumber;

    
    private static $initialised = false;
    
    private static $separatorBeforeNationalNumber = ' ';
    
    private static $emptyMetadata;

    
    private static $eligibleFormatPattern;

    
    private static $nationalPrefixSeparatorsPattern = '[- ]';

    
    private static $minLeadingDigitsLength = 3;

    
    private static $digitPattern = "\xE2\x80\x88";


    private static function init()
    {
        if (self::$initialised === false) {
            self::$initialised = true;

            self::$emptyMetadata = new PhoneMetadata();
            self::$emptyMetadata->setInternationalPrefix('NA');

            self::$eligibleFormatPattern = '[' . PhoneNumberUtil::VALID_PUNCTUATION . ']*'
                . "\\$1" . "[" . PhoneNumberUtil::VALID_PUNCTUATION . "]*(\\$\\d"
                . "[" . PhoneNumberUtil::VALID_PUNCTUATION . "]*)*";
        }
    }

    
    public function __construct($regionCode)
    {
        self::init();

        $this->phoneUtil = PhoneNumberUtil::getInstance();

        $this->defaultCountry = strtoupper($regionCode);
        $this->currentMetadata = $this->getMetadataForRegion($this->defaultCountry);
        $this->defaultMetadata = $this->currentMetadata;
    }

    
    private function getMetadataForRegion($regionCode)
    {
        $countryCallingCode = $this->phoneUtil->getCountryCodeForRegion($regionCode);
        $mainCountry = $this->phoneUtil->getRegionCodeForCountryCode($countryCallingCode);
        $metadata = $this->phoneUtil->getMetadataForRegion($mainCountry);
        if ($metadata !== null) {
            return $metadata;
        }
        
        
        return self::$emptyMetadata;
    }

    
    private function maybeCreateNewTemplate()
    {
        
        
        foreach ($this->possibleFormats as $key => $numberFormat) {
            $pattern = $numberFormat->getPattern();
            if ($this->currentFormattingPattern == $pattern) {
                return false;
            }
            if ($this->createFormattingTemplate($numberFormat)) {
                $this->currentFormattingPattern = $pattern;
                $nationalPrefixSeparatorsMatcher = new Matcher(
                    self::$nationalPrefixSeparatorsPattern,
                    $numberFormat->getNationalPrefixFormattingRule()
                );
                $this->shouldAddSpaceAfterNationalPrefix = $nationalPrefixSeparatorsMatcher->find();
                
                
                $this->lastMatchPosition = 0;
                return true;
            }

            
            unset($this->possibleFormats[$key]);
        }
        $this->ableToFormat = false;
        return false;
    }

    
    private function getAvailableFormats($leadingDigits)
    {
        
        $isInternationalNumber = $this->isCompleteNumber && $this->extractedNationalPrefix === '';

        $formatList = ($isInternationalNumber && $this->currentMetadata->intlNumberFormatSize() > 0)
            ? $this->currentMetadata->intlNumberFormats()
            : $this->currentMetadata->numberFormats();

        foreach ($formatList as $format) {
            
            
            if ($this->extractedNationalPrefix !== ''
                && PhoneNumberUtil::formattingRuleHasFirstGroupOnly(
                    $format->getNationalPrefixFormattingRule()
                )
                && !$format->getNationalPrefixOptionalWhenFormatting()
                && !$format->hasDomesticCarrierCodeFormattingRule()) {
                
                
                
                
                continue;
            }

            if ($this->extractedNationalPrefix === ''
                && !$this->isCompleteNumber
                && !PhoneNumberUtil::formattingRuleHasFirstGroupOnly(
                    $format->getNationalPrefixFormattingRule()
                )
                && !$format->getNationalPrefixOptionalWhenFormatting()) {
                
                
                continue;
            }

            $eligibleFormatMatcher = new Matcher(self::$eligibleFormatPattern, $format->getFormat());

            if ($eligibleFormatMatcher->matches()) {
                $this->possibleFormats[] = $format;
            }
        }
        $this->narrowDownPossibleFormats($leadingDigits);
    }

    
    private function narrowDownPossibleFormats($leadingDigits)
    {
        $indexOfLeadingDigitsPattern = \mb_strlen($leadingDigits) - self::$minLeadingDigitsLength;

        foreach ($this->possibleFormats as $key => $format) {
            if ($format->leadingDigitsPatternSize() === 0) {
                
                continue;
            }
            $lastLeadingDigitsPattern = \min($indexOfLeadingDigitsPattern, $format->leadingDigitsPatternSize() - 1);
            $leadingDigitsPattern = $format->getLeadingDigitsPattern($lastLeadingDigitsPattern);
            $m = new Matcher($leadingDigitsPattern, $leadingDigits);
            if (!$m->lookingAt()) {
                unset($this->possibleFormats[$key]);
            }
        }
    }

    
    private function createFormattingTemplate(NumberFormat $format)
    {
        $numberPattern = $format->getPattern();

        $this->formattingTemplate = '';
        $tempTemplate = $this->getFormattingTemplate($numberPattern, $format->getFormat());
        if ($tempTemplate !== '') {
            $this->formattingTemplate .= $tempTemplate;
            return true;
        }
        return false;
    }

    
    private function getFormattingTemplate($numberPattern, $numberFormat)
    {
        
        
        $longestPhoneNumber = '999999999999999';
        $m = new Matcher($numberPattern, $longestPhoneNumber);
        $m->find();
        $aPhoneNumber = $m->group();
        
        
        if (\mb_strlen($aPhoneNumber) < \mb_strlen($this->nationalNumber)) {
            return '';
        }
        
        $template = \preg_replace('/' . $numberPattern . '/' . PhoneNumberUtil::REGEX_FLAGS, $numberFormat, $aPhoneNumber);
        
        $template = \preg_replace('/9/', self::$digitPattern, $template);
        return $template;
    }

    
    public function clear()
    {
        $this->currentOutput = '';
        $this->accruedInput = '';
        $this->accruedInputWithoutFormatting = '';
        $this->formattingTemplate = '';
        $this->lastMatchPosition = 0;
        $this->currentFormattingPattern = '';
        $this->prefixBeforeNationalNumber = '';
        $this->extractedNationalPrefix = '';
        $this->nationalNumber = '';
        $this->ableToFormat = true;
        $this->inputHasFormatting = false;
        $this->positionToRemember = 0;
        $this->originalPosition = 0;
        $this->isCompleteNumber = false;
        $this->isExpectingCountryCallingCode = false;
        $this->possibleFormats = array();
        $this->shouldAddSpaceAfterNationalPrefix = false;
        if ($this->currentMetadata !== $this->defaultMetadata) {
            $this->currentMetadata = $this->getMetadataForRegion($this->defaultCountry);
        }
    }

    
    public function inputDigit($nextChar)
    {
        $this->currentOutput = $this->inputDigitWithOptionToRememberPosition($nextChar, false);
        return $this->currentOutput;
    }

    
    public function inputDigitAndRememberPosition($nextChar)
    {
        $this->currentOutput = $this->inputDigitWithOptionToRememberPosition($nextChar, true);
        return $this->currentOutput;
    }

    
    private function inputDigitWithOptionToRememberPosition($nextChar, $rememberPosition)
    {
        $this->accruedInput .= $nextChar;
        if ($rememberPosition) {
            $this->originalPosition = \mb_strlen($this->accruedInput);
        }
        
        
        if (!$this->isDigitOrLeadingPlusSign($nextChar)) {
            $this->ableToFormat = false;
            $this->inputHasFormatting = true;
        } else {
            $nextChar = $this->normalizeAndAccrueDigitsAndPlusSign($nextChar, $rememberPosition);
        }
        if (!$this->ableToFormat) {
            
            
            
            if ($this->inputHasFormatting) {
                return $this->accruedInput;
            }

            if ($this->attemptToExtractIdd()) {
                if ($this->attemptToExtractCountryCallingCode()) {
                    return $this->attemptToChoosePatternWithPrefixExtracted();
                }
            } elseif ($this->ableToExtractLongerNdd()) {
                
                
                
                $this->prefixBeforeNationalNumber .= self::$separatorBeforeNationalNumber;
                return $this->attemptToChoosePatternWithPrefixExtracted();
            }
            return $this->accruedInput;
        }

        
        
        switch (\mb_strlen($this->accruedInputWithoutFormatting)) {
            case 0:
            case 1:
            case 2:
                return $this->accruedInput;
                
            case 3:
                if ($this->attemptToExtractIdd()) {
                    $this->isExpectingCountryCallingCode = true;
                } else {
                    
                    $this->extractedNationalPrefix = $this->removeNationalPrefixFromNationalNumber();
                    return $this->attemptToChooseFormattingPattern();
                }
                
                
            default:
                if ($this->isExpectingCountryCallingCode) {
                    if ($this->attemptToExtractCountryCallingCode()) {
                        $this->isExpectingCountryCallingCode = false;
                    }
                    return $this->prefixBeforeNationalNumber . $this->nationalNumber;
                }
                if (\count($this->possibleFormats) > 0) {
                    
                    $tempNationalNumber = $this->inputDigitHelper($nextChar);
                    
                    
                    $formattedNumber = $this->attemptToFormatAccruedDigits();
                    if ($formattedNumber !== '') {
                        return $formattedNumber;
                    }
                    $this->narrowDownPossibleFormats($this->nationalNumber);
                    if ($this->maybeCreateNewTemplate()) {
                        return $this->inputAccruedNationalNumber();
                    }

                    return $this->ableToFormat
                        ? $this->appendNationalNumber($tempNationalNumber)
                        : $this->accruedInput;
                }

                return $this->attemptToChooseFormattingPattern();
        }
    }

    
    private function attemptToChoosePatternWithPrefixExtracted()
    {
        $this->ableToFormat = true;
        $this->isExpectingCountryCallingCode = false;
        $this->possibleFormats = array();
        $this->lastMatchPosition = 0;
        $this->formattingTemplate = '';
        $this->currentFormattingPattern = '';
        return $this->attemptToChooseFormattingPattern();
    }

    
    public function getExtractedNationalPrefix()
    {
        return $this->extractedNationalPrefix;
    }

    
    private function ableToExtractLongerNdd()
    {
        if (\mb_strlen($this->extractedNationalPrefix) > 0) {
            
            $this->nationalNumber = $this->extractedNationalPrefix . $this->nationalNumber;
            
            
            
            $indexOfPreviousNdd = \mb_strrpos($this->prefixBeforeNationalNumber, $this->extractedNationalPrefix);
            $this->prefixBeforeNationalNumber = \mb_substr(\str_pad($this->prefixBeforeNationalNumber, $indexOfPreviousNdd), 0, $indexOfPreviousNdd);
        }
        return ($this->extractedNationalPrefix !== $this->removeNationalPrefixFromNationalNumber());
    }

    
    private function isDigitOrLeadingPlusSign($nextChar)
    {
        $plusCharsMatcher = new Matcher(PhoneNumberUtil::$PLUS_CHARS_PATTERN, $nextChar);

        return \preg_match('/' . PhoneNumberUtil::DIGITS . '/' . PhoneNumberUtil::REGEX_FLAGS, $nextChar)
            || (\mb_strlen($this->accruedInput) === 1 &&
                $plusCharsMatcher->matches());
    }

    
    public function attemptToFormatAccruedDigits()
    {
        foreach ($this->possibleFormats as $numberFormat) {
            $m = new Matcher($numberFormat->getPattern(), $this->nationalNumber);
            if ($m->matches()) {
                $nationalPrefixSeparatorsMatcher = new Matcher(self::$nationalPrefixSeparatorsPattern, $numberFormat->getNationalPrefixFormattingRule());
                $this->shouldAddSpaceAfterNationalPrefix = $nationalPrefixSeparatorsMatcher->find();
                $formattedNumber = $m->replaceAll($numberFormat->getFormat());
                
                
                
                
                
                
                $fullOutput = $this->appendNationalNumber($formattedNumber);
                $formattedNumberDigitsOnly = PhoneNumberUtil::normalizeDiallableCharsOnly($fullOutput);

                if ($formattedNumberDigitsOnly === $this->accruedInputWithoutFormatting) {
                    
                    
                    return $fullOutput;
                }
            }
        }
        return '';
    }

    
    public function getRememberedPosition()
    {
        if (!$this->ableToFormat) {
            return $this->originalPosition;
        }

        $accruedInputIndex = 0;
        $currentOutputIndex = 0;
        $currentOutputLength = \mb_strlen($this->currentOutput);
        while ($accruedInputIndex < $this->positionToRemember && $currentOutputIndex < $currentOutputLength) {
            if (\mb_substr($this->accruedInputWithoutFormatting, $accruedInputIndex, 1) == \mb_substr($this->currentOutput, $currentOutputIndex, 1)) {
                $accruedInputIndex++;
            }
            $currentOutputIndex++;
        }
        return $currentOutputIndex;
    }

    
    private function appendNationalNumber($nationalNumber)
    {
        $prefixBeforeNationalNumberLength = \mb_strlen($this->prefixBeforeNationalNumber);
        if ($this->shouldAddSpaceAfterNationalPrefix && $prefixBeforeNationalNumberLength > 0
            && \mb_substr($this->prefixBeforeNationalNumber, $prefixBeforeNationalNumberLength - 1, 1)
            != self::$separatorBeforeNationalNumber
        ) {
            
            
            
            return $this->prefixBeforeNationalNumber . self::$separatorBeforeNationalNumber . $nationalNumber;
        }

        return $this->prefixBeforeNationalNumber . $nationalNumber;
    }

    
    private function attemptToChooseFormattingPattern()
    {
        
        
        if (\mb_strlen($this->nationalNumber) >= self::$minLeadingDigitsLength) {
            $this->getAvailableFormats($this->nationalNumber);
            
            $formattedNumber = $this->attemptToFormatAccruedDigits();
            if ($formattedNumber !== '') {
                return $formattedNumber;
            }
            return $this->maybeCreateNewTemplate() ? $this->inputAccruedNationalNumber() : $this->accruedInput;
        }

        return $this->appendNationalNumber($this->nationalNumber);
    }

    
    private function inputAccruedNationalNumber()
    {
        $lengthOfNationalNumber = \mb_strlen($this->nationalNumber);
        if ($lengthOfNationalNumber > 0) {
            $tempNationalNumber = '';
            for ($i = 0; $i < $lengthOfNationalNumber; $i++) {
                $tempNationalNumber = $this->inputDigitHelper(\mb_substr($this->nationalNumber, $i, 1));
            }
            return $this->ableToFormat ? $this->appendNationalNumber($tempNationalNumber) : $this->accruedInput;
        }

        return $this->prefixBeforeNationalNumber;
    }

    
    private function isNanpaNumberWithNationalPrefix()
    {
        
        
        
        
        return ($this->currentMetadata->getCountryCode() == 1) && (\mb_substr($this->nationalNumber, 0, 1) == '1')
            && (\mb_substr($this->nationalNumber, 1, 1) != '0') && (\mb_substr($this->nationalNumber, 1, 1) != '1');
    }

    
    private function removeNationalPrefixFromNationalNumber()
    {
        $startOfNationalNumber = 0;
        if ($this->isNanpaNumberWithNationalPrefix()) {
            $startOfNationalNumber = 1;
            $this->prefixBeforeNationalNumber .= '1' . self::$separatorBeforeNationalNumber;
            $this->isCompleteNumber = true;
        } elseif ($this->currentMetadata->hasNationalPrefixForParsing()) {
            $m = new Matcher($this->currentMetadata->getNationalPrefixForParsing(), $this->nationalNumber);
            
            
            if ($m->lookingAt() && $m->end() > 0) {
                
                
                
                $this->isCompleteNumber = true;
                $startOfNationalNumber = $m->end();
                $this->prefixBeforeNationalNumber .= \mb_substr($this->nationalNumber, 0, $startOfNationalNumber);
            }
        }
        $nationalPrefix = \mb_substr($this->nationalNumber, 0, $startOfNationalNumber);
        $this->nationalNumber = \mb_substr($this->nationalNumber, $startOfNationalNumber);
        return $nationalPrefix;
    }

    
    private function attemptToExtractIdd()
    {
        $internationalPrefix = "\\" . PhoneNumberUtil::PLUS_SIGN . '|' . $this->currentMetadata->getInternationalPrefix();
        $iddMatcher = new Matcher($internationalPrefix, $this->accruedInputWithoutFormatting);

        if ($iddMatcher->lookingAt()) {
            $this->isCompleteNumber = true;
            $startOfCountryCallingCode = $iddMatcher->end();
            $this->nationalNumber = \mb_substr($this->accruedInputWithoutFormatting, $startOfCountryCallingCode);
            $this->prefixBeforeNationalNumber = \mb_substr($this->accruedInputWithoutFormatting, 0, $startOfCountryCallingCode);
            if (\mb_substr($this->accruedInputWithoutFormatting, 0, 1) != PhoneNumberUtil::PLUS_SIGN) {
                $this->prefixBeforeNationalNumber .= self::$separatorBeforeNationalNumber;
            }
            return true;
        }
        return false;
    }

    
    private function attemptToExtractCountryCallingCode()
    {
        if ($this->nationalNumber === '') {
            return false;
        }
        $numberWithoutCountryCallingCode = '';
        $countryCode = $this->phoneUtil->extractCountryCode($this->nationalNumber, $numberWithoutCountryCallingCode);
        if ($countryCode === 0) {
            return false;
        }
        $this->nationalNumber = $numberWithoutCountryCallingCode;
        $newRegionCode = $this->phoneUtil->getRegionCodeForCountryCode($countryCode);
        if (PhoneNumberUtil::REGION_CODE_FOR_NON_GEO_ENTITY == $newRegionCode) {
            $this->currentMetadata = $this->phoneUtil->getMetadataForNonGeographicalRegion($countryCode);
        } elseif ($newRegionCode != $this->defaultCountry) {
            $this->currentMetadata = $this->getMetadataForRegion($newRegionCode);
        }
        $countryCodeString = (string)$countryCode;
        $this->prefixBeforeNationalNumber .= $countryCodeString . self::$separatorBeforeNationalNumber;
        
        
        $this->extractedNationalPrefix = '';
        return true;
    }

    
    private function normalizeAndAccrueDigitsAndPlusSign($nextChar, $rememberPosition)
    {
        if ($nextChar == PhoneNumberUtil::PLUS_SIGN) {
            $normalizedChar = $nextChar;
            $this->accruedInputWithoutFormatting .= $nextChar;
        } else {
            $normalizedChar = PhoneNumberUtil::normalizeDigits($nextChar, false);
            $this->accruedInputWithoutFormatting .= $normalizedChar;
            $this->nationalNumber .= $normalizedChar;
        }
        if ($rememberPosition) {
            $this->positionToRemember = \mb_strlen($this->accruedInputWithoutFormatting);
        }
        return $normalizedChar;
    }

    
    private function inputDigitHelper($nextChar)
    {
        
        
        $digitMatcher = new Matcher(self::$digitPattern, $this->formattingTemplate);
        if ($digitMatcher->find($this->lastMatchPosition)) {
            $tempTemplate = $digitMatcher->replaceFirst($nextChar);
            $this->formattingTemplate = $tempTemplate . \mb_substr($this->formattingTemplate, \mb_strlen(
                $tempTemplate,
                'UTF-8'
            ), null, 'UTF-8');
            $this->lastMatchPosition = $digitMatcher->start();
            return \mb_substr($this->formattingTemplate, 0, $this->lastMatchPosition + 1);
        }

        if (\count($this->possibleFormats) === 1) {
            
            
            $this->ableToFormat = false;
        } 
        $this->currentFormattingPattern = '';
        return $this->accruedInput;
    }
}

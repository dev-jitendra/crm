<?php

namespace libphonenumber\Leniency;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberMatcher;
use libphonenumber\PhoneNumberUtil;

class ExactGrouping extends AbstractLeniency
{
    protected static $level = 4;

    
    public static function verify(PhoneNumber $number, $candidate, PhoneNumberUtil $util)
    {
        if (!$util->isValidNumber($number)
            || !PhoneNumberMatcher::containsOnlyValidXChars($number, $candidate, $util)
            || PhoneNumberMatcher::containsMoreThanOneSlashInNationalNumber($number, $candidate)
            || !PhoneNumberMatcher::isNationalPrefixPresentIfRequired($number, $util)
        ) {
            return false;
        }

        return PhoneNumberMatcher::checkNumberGroupingIsValid(
            $number,
            $candidate,
            $util,
            function (PhoneNumberUtil $util, PhoneNumber $number, $normalizedCandidate, $expectedNumberGroups) {
                return PhoneNumberMatcher::allNumberGroupsAreExactlyPresent(
                    $util,
                    $number,
                    $normalizedCandidate,
                    $expectedNumberGroups
                );
            }
        );
    }
}

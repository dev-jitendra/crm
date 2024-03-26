<?php

namespace libphonenumber;


class RegexBasedMatcher implements MatcherAPIInterface
{
    public static function create()
    {
        return new static();
    }

    
    public function matchNationalNumber($number, PhoneNumberDesc $numberDesc, $allowPrefixMatch)
    {
        $nationalNumberPattern = $numberDesc->getNationalNumberPattern();

        
        

        if (\strlen($nationalNumberPattern) === 0) {
            return false;
        }

        return $this->match($number, $nationalNumberPattern, $allowPrefixMatch);
    }

    
    private function match($number, $pattern, $allowPrefixMatch)
    {
        $matcher = new Matcher($pattern, $number);

        if (!$matcher->lookingAt()) {
            return false;
        }

        return $matcher->matches() ? true : $allowPrefixMatch;
    }
}

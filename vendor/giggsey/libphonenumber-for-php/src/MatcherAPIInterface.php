<?php

namespace libphonenumber;


interface MatcherAPIInterface
{
    
    public function matchNationalNumber($number, PhoneNumberDesc $numberDesc, $allowPrefixMatch);
}

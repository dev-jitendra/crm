<?php



return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '1\\d{2,3}',
    'PossibleLength' => 
    array (
      0 => 3,
      1 => 4,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'tollFree' => 
  array (
    'NationalNumberPattern' => '1(?:0[0-2]|66)',
    'ExampleNumber' => '100',
    'PossibleLength' => 
    array (
      0 => 3,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'premiumRate' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'emergency' => 
  array (
    'NationalNumberPattern' => '10[0-2]',
    'ExampleNumber' => '100',
    'PossibleLength' => 
    array (
      0 => 3,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'shortCode' => 
  array (
    'NationalNumberPattern' => '1(?:0[0-2]|122|44|66|99)',
    'ExampleNumber' => '100',
    'PossibleLength' => 
    array (
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'standardRate' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'carrierSpecific' => 
  array (
    'NationalNumberPattern' => '112\\d',
    'ExampleNumber' => '1120',
    'PossibleLength' => 
    array (
      0 => 4,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'smsServices' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'id' => 'PS',
  'countryCode' => 0,
  'internationalPrefix' => '',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' => 
  array (
  ),
  'intlNumberFormat' => 
  array (
  ),
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
);

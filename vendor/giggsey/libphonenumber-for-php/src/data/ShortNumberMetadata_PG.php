<?php



return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '[01]\\d{2,6}',
    'PossibleLength' => 
    array (
      0 => 3,
      1 => 4,
      2 => 5,
      3 => 6,
      4 => 7,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'tollFree' => 
  array (
    'NationalNumberPattern' => '000|11[01]',
    'ExampleNumber' => '000',
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
    'NationalNumberPattern' => '000|11[01]',
    'ExampleNumber' => '000',
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
    'NationalNumberPattern' => '000|1(?:1[01]|5\\d\\d|6\\d{2,5})',
    'ExampleNumber' => '000',
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
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'smsServices' => 
  array (
    'NationalNumberPattern' => '16\\d{2,5}',
    'ExampleNumber' => '1600',
    'PossibleLength' => 
    array (
      0 => 4,
      1 => 5,
      2 => 6,
      3 => 7,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'id' => 'PG',
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

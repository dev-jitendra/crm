<?php



return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '[14]\\d{2,3}',
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
    'NationalNumberPattern' => '10[1-3]',
    'ExampleNumber' => '101',
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
    'NationalNumberPattern' => '10[1-3]',
    'ExampleNumber' => '101',
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
    'NationalNumberPattern' => '10[1-3]|4040',
    'ExampleNumber' => '101',
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
    'NationalNumberPattern' => '404\\d',
    'ExampleNumber' => '4040',
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
    'NationalNumberPattern' => '404\\d',
    'ExampleNumber' => '4040',
    'PossibleLength' => 
    array (
      0 => 4,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'id' => 'KG',
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

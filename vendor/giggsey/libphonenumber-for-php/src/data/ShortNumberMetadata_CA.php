<?php



return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '[1-9]\\d\\d(?:\\d\\d(?:\\d(?:\\d{2})?)?)?',
    'PossibleLength' => 
    array (
      0 => 3,
      1 => 5,
      2 => 6,
      3 => 8,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'tollFree' => 
  array (
    'NationalNumberPattern' => '112|988|[29]11',
    'ExampleNumber' => '112',
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
    'NationalNumberPattern' => '112|911',
    'ExampleNumber' => '112',
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
    'NationalNumberPattern' => '112|30000\\d{3}|[1-35-9]\\d{4,5}|[2-8]11|9(?:11|88)',
    'ExampleNumber' => '112',
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
    'NationalNumberPattern' => '[235-7]11',
    'ExampleNumber' => '211',
    'PossibleLength' => 
    array (
      0 => 3,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'smsServices' => 
  array (
    'NationalNumberPattern' => '300\\d{5}|[1-35-9]\\d{4,5}',
    'ExampleNumber' => '10000',
    'PossibleLength' => 
    array (
      0 => 5,
      1 => 6,
      2 => 8,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'id' => 'CA',
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

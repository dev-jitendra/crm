<?php



return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '[136-8]\\d{1,4}',
    'PossibleLength' => 
    array (
      0 => 2,
      1 => 3,
      2 => 4,
      3 => 5,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'tollFree' => 
  array (
    'NationalNumberPattern' => '1[578]|(?:352|67)00|7402|(?:677|744|8000)\\d',
    'ExampleNumber' => '15',
    'PossibleLength' => 
    array (
      0 => 2,
      1 => 4,
      2 => 5,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'premiumRate' => 
  array (
    'NationalNumberPattern' => '(?:12|800)2\\d|3(?:52(?:11|2[02]|3[04-6]|99)|7574)',
    'ExampleNumber' => '1220',
    'PossibleLength' => 
    array (
      0 => 4,
      1 => 5,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'emergency' => 
  array (
    'NationalNumberPattern' => '1[578]',
    'ExampleNumber' => '15',
    'PossibleLength' => 
    array (
      0 => 2,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'shortCode' => 
  array (
    'NationalNumberPattern' => '1(?:1(?:[013-9]\\d|2)|2(?:1[02-469]|2[13])|[578])|350(?:35|57)|67(?:0[09]|[59]9|77|8[89])|74(?:0[02]|44|55)|800[0-2][12]|3(?:52|[67]\\d)\\d\\d',
    'ExampleNumber' => '15',
    'PossibleLength' => 
    array (
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'standardRate' => 
  array (
    'NationalNumberPattern' => '37(?:433|575)|7400|8001\\d',
    'ExampleNumber' => '7400',
    'PossibleLength' => 
    array (
      0 => 4,
      1 => 5,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'carrierSpecific' => 
  array (
    'NationalNumberPattern' => '3503\\d|(?:3[67]\\d|800)\\d\\d',
    'ExampleNumber' => '35030',
    'PossibleLength' => 
    array (
      0 => 5,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'smsServices' => 
  array (
    'NationalNumberPattern' => '374(?:0[24-9]|[1-9]\\d)|7400|3(?:6\\d|75)\\d\\d',
    'ExampleNumber' => '7400',
    'PossibleLength' => 
    array (
      0 => 4,
      1 => 5,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'id' => 'ML',
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

<?php



return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '[14]\\d{2,6}',
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
    'NationalNumberPattern' => '1(?:1(?:[2358]|6\\d{3})|87)',
    'ExampleNumber' => '112',
    'PossibleLength' => 
    array (
      0 => 3,
      1 => 6,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'premiumRate' => 
  array (
    'NationalNumberPattern' => '(?:12|4(?:[478](?:[0-4]|[5-9]\\d\\d)|55))\\d\\d',
    'ExampleNumber' => '1200',
    'PossibleLength' => 
    array (
      0 => 4,
      1 => 5,
      2 => 7,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'emergency' => 
  array (
    'NationalNumberPattern' => '11[2358]',
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
    'NationalNumberPattern' => '1(?:0\\d{2,3}|1(?:[2-57-9]|6(?:000|111))|3[39]|4(?:82|9\\d{1,3})|5(?:00|1[58]|2[25]|3[03]|44|[59])|60|8[67]|9(?:[01]|2[2-9]|4\\d|696))|4(?:2323|5045)|(?:1(?:2|92[01])|4(?:3(?:[01]|[45]\\d\\d)|[478](?:[0-4]|[5-9]\\d\\d)|55))\\d\\d',
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
    'NationalNumberPattern' => '4(?:3(?:[01]|[45]\\d\\d)|[478](?:[0-4]|[5-9]\\d\\d)|5[05])\\d\\d',
    'ExampleNumber' => '43000',
    'PossibleLength' => 
    array (
      0 => 5,
      1 => 7,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'id' => 'IT',
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

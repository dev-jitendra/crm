<?php



return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '[1579]\\d{2,4}',
    'PossibleLength' => 
    array (
      0 => 3,
      1 => 4,
      2 => 5,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'tollFree' => 
  array (
    'NationalNumberPattern' => '10[0-26]|[19]99',
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
    'NationalNumberPattern' => '10[0-2]|[19]99',
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
    'NationalNumberPattern' => '1(?:0(?:[0-369]|5[1-4]|7[0-4]|8[0-29])|1[16-9]|2(?:[134]|2[0-5])|3(?:1\\d?|6[3-6])|5[2-9])|5012|786|9594|[19]99|1(?:0(?:50|6\\d)|33|4(?:0|1\\d))\\d',
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
    'NationalNumberPattern' => '1(?:11|2[13])|(?:501|959)\\d|786',
    'ExampleNumber' => '111',
    'PossibleLength' => 
    array (
      0 => 3,
      1 => 4,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'smsServices' => 
  array (
    'NationalNumberPattern' => '959\\d',
    'ExampleNumber' => '9590',
    'PossibleLength' => 
    array (
      0 => 4,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'id' => 'BD',
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

<?php



return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '[01389]\\d{1,4}',
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
    'NationalNumberPattern' => '000|1(?:0[0-35-7]|1[0245]|2[015]|3[47]|4[478]|9)|911',
    'ExampleNumber' => '19',
    'PossibleLength' => 
    array (
      0 => 2,
      1 => 3,
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
    'NationalNumberPattern' => '10[017]|911',
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
    'NationalNumberPattern' => '000|1(?:0[0-35-7]|1[02-5]|2[015]|3[47]|4[478]|9)|3372|89338|911',
    'ExampleNumber' => '19',
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
    'NationalNumberPattern' => '893\\d\\d',
    'ExampleNumber' => '89300',
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
    'NationalNumberPattern' => '(?:337|893\\d)\\d',
    'ExampleNumber' => '3370',
    'PossibleLength' => 
    array (
      0 => 4,
      1 => 5,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'id' => 'AR',
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

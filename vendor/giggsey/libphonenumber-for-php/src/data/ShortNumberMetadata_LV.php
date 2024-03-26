<?php



return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '[018]\\d{1,5}',
    'PossibleLength' => 
    array (
      0 => 2,
      1 => 3,
      2 => 4,
      3 => 5,
      4 => 6,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'tollFree' => 
  array (
    'NationalNumberPattern' => '0[1-3]|11(?:[023]|6\\d{3})',
    'ExampleNumber' => '01',
    'PossibleLength' => 
    array (
      0 => 2,
      1 => 3,
      2 => 6,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'premiumRate' => 
  array (
    'NationalNumberPattern' => '1180|821\\d\\d',
    'ExampleNumber' => '1180',
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
    'NationalNumberPattern' => '0[1-3]|11[023]',
    'ExampleNumber' => '01',
    'PossibleLength' => 
    array (
      0 => 2,
      1 => 3,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'shortCode' => 
  array (
    'NationalNumberPattern' => '0[1-4]|1(?:1(?:[02-4]|6(?:000|111)|8[0189])|(?:5|65)5|77)|821[57]4',
    'ExampleNumber' => '01',
    'PossibleLength' => 
    array (
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'standardRate' => 
  array (
    'NationalNumberPattern' => '1181',
    'ExampleNumber' => '1181',
    'PossibleLength' => 
    array (
      0 => 4,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'carrierSpecific' => 
  array (
    'NationalNumberPattern' => '165\\d',
    'ExampleNumber' => '1650',
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
  'id' => 'LV',
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

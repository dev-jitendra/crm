<?php



return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '[27]\\d{6,7}|[34]\\d{5,7}|63\\d{6}|(?:5|8\\d\\d)\\d{7}',
    'PossibleLength' => 
    array (
      0 => 6,
      1 => 7,
      2 => 8,
      3 => 10,
    ),
    'PossibleLengthLocalOnly' => 
    array (
      0 => 4,
      1 => 5,
    ),
  ),
  'fixedLine' => 
  array (
    'NationalNumberPattern' => '(?:3[23]|4[89])\\d{4,6}|(?:31|4[36]|8(?:0[25]|78)\\d)\\d{6}|(?:2[1-4]|4[1257]|7\\d)\\d{5,6}',
    'ExampleNumber' => '71234567',
    'PossibleLength' => 
    array (
    ),
    'PossibleLengthLocalOnly' => 
    array (
      0 => 4,
      1 => 5,
    ),
  ),
  'mobile' => 
  array (
    'NationalNumberPattern' => '(?:5\\d|63)\\d{6}',
    'ExampleNumber' => '51234567',
    'PossibleLength' => 
    array (
      0 => 8,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'tollFree' => 
  array (
    'NationalNumberPattern' => '800\\d{7}',
    'ExampleNumber' => '8001234567',
    'PossibleLength' => 
    array (
      0 => 10,
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
  'sharedCost' => 
  array (
    'NationalNumberPattern' => '807\\d{7}',
    'ExampleNumber' => '8071234567',
    'PossibleLength' => 
    array (
      0 => 10,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'personalNumber' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'voip' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'pager' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'uan' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'voicemail' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'noInternationalDialling' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'id' => 'CU',
  'countryCode' => 53,
  'internationalPrefix' => '119',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' => 
  array (
    0 => 
    array (
      'pattern' => '(\\d{2})(\\d{4,6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' => 
      array (
        0 => '2[1-4]|[34]',
      ),
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ),
    1 => 
    array (
      'pattern' => '(\\d)(\\d{6,7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' => 
      array (
        0 => '7',
      ),
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ),
    2 => 
    array (
      'pattern' => '(\\d)(\\d{7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' => 
      array (
        0 => '[56]',
      ),
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ),
    3 => 
    array (
      'pattern' => '(\\d{3})(\\d{7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' => 
      array (
        0 => '8',
      ),
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ),
  ),
  'intlNumberFormat' => 
  array (
  ),
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
);

<?php




return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'M/D/YY',
    ],
    'months' => ['جنؤری', 'فرؤری', 'مارٕچ', 'اپریل', 'میٔ', 'جوٗن', 'جوٗلایی', 'اگست', 'ستمبر', 'اکتوٗبر', 'نومبر', 'دسمبر'],
    'months_short' => ['جنؤری', 'فرؤری', 'مارٕچ', 'اپریل', 'میٔ', 'جوٗن', 'جوٗلایی', 'اگست', 'ستمبر', 'اکتوٗبر', 'نومبر', 'دسمبر'],
    'weekdays' => ['آتهوار', 'ژءندروار', 'بوءںوار', 'بودهوار', 'برىسوار', 'جمع', 'بٹوار'],
    'weekdays_short' => ['آتهوار', 'ژءنتروار', 'بوءںوار', 'بودهوار', 'برىسوار', 'جمع', 'بٹوار'],
    'weekdays_min' => ['آتهوار', 'ژءنتروار', 'بوءںوار', 'بودهوار', 'برىسوار', 'جمع', 'بٹوار'],
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['دوپھربرونھ', 'دوپھرپتھ'],

    'year' => ':count آب', 
    'y' => ':count آب', 
    'a_year' => ':count آب', 

    'month' => ':count रान्', 
    'm' => ':count रान्', 
    'a_month' => ':count रान्', 

    'week' => ':count آتھٕوار', 
    'w' => ':count آتھٕوار', 
    'a_week' => ':count آتھٕوار', 

    'hour' => ':count سۄن', 
    'h' => ':count سۄن', 
    'a_hour' => ':count سۄن', 

    'minute' => ':count فَن', 
    'min' => ':count فَن', 
    'a_minute' => ':count فَن', 

    'second' => ':count दोʼयुम', 
    's' => ':count दोʼयुम', 
    'a_second' => ':count दोʼयुम', 
]);

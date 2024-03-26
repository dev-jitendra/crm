<?php




return array_replace_recursive(require __DIR__.'/sd.php', [
    'formats' => [
        'L' => 'D/M/YY',
    ],
    'months' => ['جنوري', 'فبروري', 'مارچ', 'اپريل', 'مي', 'جون', 'جولاءِ', 'آگسٽ', 'سيپٽيمبر', 'آڪٽوبر', 'نومبر', 'ڊسمبر'],
    'months_short' => ['جنوري', 'فبروري', 'مارچ', 'اپريل', 'مي', 'جون', 'جولاءِ', 'آگسٽ', 'سيپٽيمبر', 'آڪٽوبر', 'نومبر', 'ڊسمبر'],
    'weekdays' => ['آرتوارُ', 'سومرُ', 'منگلُ', 'ٻُڌرُ', 'وسپت', 'جُمو', 'ڇنڇر'],
    'weekdays_short' => ['آرتوارُ', 'سومرُ', 'منگلُ', 'ٻُڌرُ', 'وسپت', 'جُمو', 'ڇنڇر'],
    'weekdays_min' => ['آرتوارُ', 'سومرُ', 'منگلُ', 'ٻُڌرُ', 'وسپت', 'جُمو', 'ڇنڇر'],
    'day_of_first_week_of_year' => 1,
]);
